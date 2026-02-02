import { http } from "./http";

export async function fetchProducts(q = "") {
  const res = await http.get("/api/products", {
    params: q ? { q } : {},
  });
  return res.data;
}

export async function fetchProduct(id) {
  const res = await http.get(`/api/products/${id}`);
  return res.data;
}
