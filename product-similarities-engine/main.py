import os
import datetime as dt
from typing import List, Optional, Tuple

import numpy as np
from fastapi import FastAPI, Header, HTTPException, Depends
from pydantic import BaseModel
from sqlalchemy import (
    create_engine, Column, String, Float, DateTime, JSON as SAJSON, PrimaryKeyConstraint, and_
)
from sqlalchemy.orm import sessionmaker, declarative_base, Session
from sqlalchemy.dialects.mysql import BIGINT as MyBIGINT, DOUBLE as MyDOUBLE, JSON as MyJSON
from dotenv import load_dotenv

load_dotenv()

# -----------------------
# Config & DB
# -----------------------
SECRET = os.getenv("ML_SERVICE_TOKEN", "supersecrettoken")
DATABASE_URL = os.getenv("DATABASE_URL")

engine = create_engine(
    DATABASE_URL,
    pool_pre_ping=True,   # avoids stale connections
    pool_recycle=3600,    # recycle hourly (MySQL timeouts)
    future=True
)
SessionLocal = sessionmaker(bind=engine, autoflush=False, autocommit=False, future=True)
Base = declarative_base()

class ProductEmbedding(Base):
    __tablename__ = "product_embeddings"
    product_id = Column(MyBIGINT(unsigned=True), primary_key=True, index=True)
    shop_id  = Column(MyBIGINT(unsigned=True), nullable=True, index=True)
    model      = Column(String(100), nullable=False, index=True)
    # Use dialect JSON if available; SAJSON also works on MySQL 5.7+/8.0
    vector     = Column(MyJSON().with_variant(SAJSON, "mysql"), nullable=False)
    updated_at = Column(DateTime, default=dt.datetime.utcnow, onupdate=dt.datetime.utcnow, nullable=False)

class SimilarProduct(Base):
    __tablename__ = "similar_products"
    product_id = Column(MyBIGINT(unsigned=True), nullable=False)
    similar_id = Column(MyBIGINT(unsigned=True), nullable=False)
    score      = Column(MyDOUBLE(asdecimal=False), nullable=False)
    __table_args__ = (
        PrimaryKeyConstraint('product_id', 'similar_id', name='pk_similar'),
    )

# Don’t create tables if Laravel owns migrations.
# Base.metadata.create_all(engine)

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# -----------------------
# Sentence-Transformers
# -----------------------
from sentence_transformers import SentenceTransformer

MODEL_NAME = "sentence-transformers/all-MiniLM-L6-v2"
_model: Optional[SentenceTransformer] = None

def get_model() -> SentenceTransformer:
    global _model
    if _model is None:
        _model = SentenceTransformer(MODEL_NAME)
    return _model

def embed(text: str) -> np.ndarray:
    model = get_model()
    vec = model.encode([text], normalize_embeddings=True)[0]
    return vec.astype(np.float32)

# -----------------------
# Similarity & Persistence
# -----------------------
def upsert_embedding(db: Session, product_id: int, shop_id: Optional[int], vec: np.ndarray, model_name: str):
    rec = db.get(ProductEmbedding, product_id)
    now = dt.datetime.utcnow()
    if rec:
        rec.shop_id  = shop_id
        rec.model      = model_name
        rec.vector     = vec.tolist()
        rec.updated_at = now
    else:
        rec = ProductEmbedding(
            product_id=product_id,
            shop_id=shop_id,
            model=model_name,
            vector=vec.tolist(),
            updated_at=now
        )
        db.add(rec)
    db.commit()

def cosine_similarity(a: np.ndarray, b: np.ndarray) -> float:
    return float(np.dot(a, b))  # normalized vectors

def find_similar(db: Session, product_id: int, shop_id: Optional[int], top_k: int = 12) -> List[Tuple[int, float]]:
    anchor: ProductEmbedding = db.get(ProductEmbedding, product_id)
    if anchor is None:
        return []
    v0 = np.array(anchor.vector, dtype=np.float32)
    if v0.size == 0:
        return []

    q = db.query(ProductEmbedding).filter(ProductEmbedding.product_id != product_id)
    if shop_id is not None:
        q = q.filter(and_(ProductEmbedding.shop_id == shop_id, ProductEmbedding.shop_id.isnot(None)))
    rows = q.all()

    sims: List[Tuple[int, float]] = []
    for r in rows:
        v = np.array(r.vector, dtype=np.float32)
        sims.append((int(r.product_id), cosine_similarity(v0, v)))

    sims.sort(key=lambda x: x[1], reverse=True)
    return sims[:top_k]

def write_similar(db: Session, product_id: int, sims: List[Tuple[int, float]]):
    db.query(SimilarProduct).filter(SimilarProduct.product_id == product_id).delete()
    if sims:
        db.bulk_save_objects([
            SimilarProduct(product_id=int(product_id), similar_id=int(pid), score=float(score))
            for pid, score in sims
        ])
    db.commit()

# -----------------------
# FastAPI
# -----------------------
app = FastAPI(title="Similar Products Service (MiniLM-L6-v2, MySQL)")

class RecomputeReq(BaseModel):
    product_id: int
    shop_id: Optional[int] = None
    title: str
    description: Optional[str] = ""
    price: Optional[float] = None
    category: Optional[str] = None
    tags: List[str] = []
    top_k: int = 12
    write_back: bool = True

class RecomputeResp(BaseModel):
    ok: bool
    wrote: Optional[int] = None
    results: Optional[List[dict]] = None

@app.get("/health")
def health():
    _ = get_model()
    return {"ok": True, "model": MODEL_NAME}

@app.post("/recompute", response_model=RecomputeResp)
def recompute(req: RecomputeReq, authorization: str = Header(None), db: Session = Depends(get_db)):
    if not authorization or not authorization.endswith(SECRET):
        raise HTTPException(status_code=401, detail="Unauthorized")

    text_parts = [
        req.title.strip(),
        (req.description or "").strip(),
        f"Category: {req.category}" if req.category else "",
        f"Tags: {', '.join(req.tags)}" if req.tags else "",
        f"Price: {req.price}" if req.price is not None else "",
    ]
    text = ". ".join([t for t in text_parts if t])

    vec = embed(text)
    upsert_embedding(db, req.product_id, req.shop_id, vec, MODEL_NAME)

    sims = find_similar(db, req.product_id, req.shop_id, top_k=req.top_k)

    if req.write_back:
        write_similar(db, req.product_id, sims)
        return RecomputeResp(ok=True, wrote=len(sims))
    else:
        return RecomputeResp(ok=True, results=[{"id": pid, "score": float(score)} for pid, score in sims])
