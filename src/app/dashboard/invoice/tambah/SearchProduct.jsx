import { Input } from "@/components/ui/input";
import fetch from "@/lib/fetch";
import React, { useEffect, useState } from "react";

export default function SearchProduct(props) {
  const { setProductInvoice, product, handleChangeProduct } = props;
  const [productInvoiceSearch, setProductInvoiceSearch] = useState(null);
  const [isLoading, setIsLoading] = useState(false);
  const [typingTimeout, setTypingTimeout] = useState(null);
  const [focus, setFocus] = useState(false);

  const handleChangeSearchProduct = (e, id) => {
    handleChangeProduct(e, id);

    const { value } = e.target;

    if (typingTimeout) clearTimeout(typingTimeout);

    const timeout = setTimeout(() => {
      if (value && value.length > 2) {
        setIsLoading(true);
        fetch
          .get("product", {
            params: {
              perPage: 5,
              search: value,
              orderBy: "name",
              orderDirection: "asc",
            },
          })
          .then((res) => {
            const products = res.data.data.data;
            setProductInvoiceSearch(products);
          })
          .catch(() => {
            setProductInvoiceSearch(null);
          })
          .finally(() => setIsLoading(false));
      }
    }, 2000);

    setTypingTimeout(timeout);
  };

  const handleNewSearchProduct = (row, id) => {
    setProductInvoice((prev) =>
      prev.map((item) =>
        item.idRow === id
          ? {
              ...item,
              name: row.name,
              unit: row.unit,
              quantity: 1,
              price: row.price,
              amount: row.price,
            }
          : item
      )
    );
    setProductInvoiceSearch(null);
  };

  return (
    <div className="relative">
      <Input
        className="bg-white border-slate-500"
        name="name"
        required
        autoComplete="off"
        onChange={(e) => handleChangeSearchProduct(e, product.idRow)}
        value={product.name}
        onFocus={() => setFocus(true)}
        onBlur={() => {
          setTimeout(() => {
            setProductInvoiceSearch(null);
            setFocus(false);
          }, 500);
        }}
      />
      {focus && (
        <div className="absolute text-xs z-10 mt-2 w-full bg-white border rounded shadow">
          {!productInvoiceSearch ? (
            <div className="px-2 py-1 hover:bg-gray-100 cursor-pointer">
              {isLoading ? (
                "mencari..."
              ) : (
                <>
                  tidak ditemukan... <br /> minimal 3 huruf
                </>
              )}
            </div>
          ) : (
            productInvoiceSearch.map((row, i) => {
              return (
                <div
                  title={row.name}
                  key={i}
                  onFocus={() => setFocus(true)}
                  onClick={() => handleNewSearchProduct(row, product.idRow)}
                  className="px-2 py-1 hover:bg-gray-100 cursor-pointer text-ellipsis block line-clamp-1">
                  {row.name}
                </div>
              );
            })
          )}
        </div>
      )}
    </div>
  );
}
