"use client";
import Box from "@/components/other/Box";
import FormDasboard from "@/components/other/FormDasboard";
import InputPassword from "@/components/other/InputPassword";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import error from "@/lib/error";
import fetch from "@/lib/fetch";
import { useRouter } from "next/navigation";
import React, { useEffect, useState } from "react";
import { toast } from "sonner";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Button } from "@/components/ui/button";
import { Plus, Trash } from "@deemlol/next-icons";
import { Checkbox } from "@/components/ui/checkbox";
import SearchProduct from "./SearchProduct";

export default function TambahForm(props) {
  const router = useRouter();
  const { pageTitle } = props;

  const [data, setData] = useState({
    // Form Atas
    order: "",
    expire: "",
    status: "paid",
    to_name: "",
    to_sales: "",
    to_address: "",
    to_telephone: "",
    to_email: "",

    // Form Bawah
    sub_total: 0,
    discount: 0,
    total: 0,
    tax: 0,
    tax_price: 0,
    grand_total: 0,
    down_payment: 0,
    remaining_balance: 0,
  });

  const [productInvoice, setProductInvoice] = useState([
    {
      idRow: 1,
      name: "",
      quantity: 1,
      unit: "",
      price: 0,
      amount: 0,
    },
  ]);

  const [loadingSubmit, setLoadingSubmit] = useState(false);

  const addRow = (e) => {
    const newId =
      productInvoice.length > 0
        ? productInvoice[productInvoice.length - 1].idRow + 1
        : 1;

    setProductInvoice([
      ...productInvoice,
      {
        idRow: newId,
        name: "",
        quantity: 1,
        unit: "",
        price: 0,
        amount: 0,
      },
    ]);
  };

  const updateQuantity = (idRow, change) => {
    setProductInvoice((prev) =>
      prev.map((product) =>
        // const amount = product.price == null ?
        product.idRow === idRow
          ? {
              ...product,
              quantity: Math.max(1, parseInt(product.quantity) + change),
              amount:
                product.price == ""
                  ? 0
                  : Math.max(1, parseInt(product.quantity) + change) *
                    product.price,
            }
          : product
      )
    );
  };

  const handleChange = (e) => {
    const { id, value } = e.target;
    setData((prevData) => ({
      ...prevData,
      [id]: value,
    }));
  };

  const handleChangeStatus = (e) => {
    setData((prevData) => ({ ...prevData, status: e }));
  };

  const handleChangeProduct = (e, idRow) => {
    const { name, value } = e.target;

    setProductInvoice((prev) =>
      prev.map((product) => {
        if (product.idRow === idRow) {
          const updatedProduct = { ...product, [name]: value };
          if (name === "quantity" || name === "price") {
            updatedProduct.amount =
              updatedProduct.quantity * updatedProduct.price;
          }

          return updatedProduct;
        }
        return product;
      })
    );
  };

  const deleteRow = (idRow) => {
    setProductInvoice(productInvoice.filter((item) => item.idRow !== idRow));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setLoadingSubmit(true);
    fetch
      .post(`invoice`, data)
      .then((response) => {
        router.push(`/dashboard/invoice/`);
        toast.success(response.data.message);
      })
      .catch((err) => {
        error(err);
      })
      .finally((e) => {
        setLoadingSubmit(false);
      });
  };

  useEffect(() => {
    const sub_total = productInvoice.reduce(
      (total, item) => total + Number(item.amount),
      0
    );

    const total = sub_total - data.discount;
    const tax_price = data?.tax ? (total * 11) / 100 : 0;
    const grand_total = total + tax_price;
    const remaining_balance = grand_total - data.down_payment;

    setData((prevData) => ({
      ...prevData,
      sub_total: sub_total,
      total: total,
      tax_price: tax_price,
      grand_total: grand_total,
      remaining_balance: remaining_balance,
      products: productInvoice,
    }));
  }, [productInvoice, data.discount, data.tax, data.down_payment]);

  return (
    <Box
      title={pageTitle}
      className="col-span-full">
      <FormDasboard
        loadingSubmit={loadingSubmit}
        className="sm:[&>div]:col-span-6"
        onSubmit={handleSubmit}>
        {/* order */}
        <div className="sm:!col-span-4">
          <Label htmlFor="order">Order</Label>
          <Input
            className="!block"
            type="date"
            id="order"
            onChange={handleChange}
            required
            value={data.order}
          />
        </div>

        <div className="sm:!col-span-4">
          <Label htmlFor="expire">Expire</Label>
          <Input
            className="!block"
            type="date"
            id="expire"
            onChange={handleChange}
            required
            value={data.expire}
          />
        </div>

        {/* status */}
        <div className="sm:!col-span-4">
          <Label htmlFor="status">Status</Label>
          <Select
            id="status"
            onValueChange={handleChangeStatus}>
            <SelectTrigger className="w-full">
              <SelectValue
                defaultValue="paid"
                placeholder="Lunas"
              />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectLabel>Baris</SelectLabel>
                <SelectItem
                  selected={data.status == "paid"}
                  value="paid">
                  Lunas
                </SelectItem>
                <SelectItem
                  selected={data.status == "unpaid"}
                  value="unpaid">
                  Belum Lunas
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
        </div>

        {/* to_name */}
        <div>
          <Label htmlFor="to_name">Nama Perusahaan</Label>
          <Input
            id="to_name"
            onChange={handleChange}
            required
            value={data.to_name}
          />
        </div>

        {/* to_sales */}
        <div>
          <Label htmlFor="to_sales">Nama Penerima</Label>
          <Input
            id="to_sales"
            onChange={handleChange}
            required
            value={data.to_sales}
          />
        </div>

        {/* to_email */}
        <div>
          <Label htmlFor="to_email">Email</Label>
          <Input
            type="email"
            id="to_email"
            onChange={handleChange}
            required
            value={data.to_email}
          />
        </div>

        {/* to_telephone */}
        <div>
          <Label htmlFor="to_telephone">Nomor Telp</Label>
          <Input
            type="number"
            id="to_telephone"
            onChange={handleChange}
            required
            value={data.to_telephone}
          />
        </div>

        {/* to_address */}

        <div className="!col-span-full">
          <Label htmlFor="to_address">Alamat</Label>
          <Textarea
            id="to_address"
            onChange={handleChange}
            required
            value={data.to_address}
          />
        </div>

        {/* Product */}
        <div className="!col-span-full border-t border-slate-300 mt-12 pt-7">
          <div className="relative !overflow-x-auto pb-10">
            <table className="w-full text-left rtl:text-right text-gray-500">
              <thead className="text-xs text-gray-700 uppercase bg-gray-100">
                <tr className="[&_th]:px-6 [&_th]:py-3 [&_th]:whitespace-nowrap">
                  <th scope="col">Produk / Jasa</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Satuan</th>
                  <th scope="col">Harga</th>
                  <th scope="col">Jumlah</th>
                  <th scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody className="[&_tr]:even:bg-slate-50 [&_tr]:hover:bg-slate-50 [&_td]:border-b [&_td]:px-6 [&_td]:py-3 [&_td]:whitespace-nowrap [&_td]:min-w-44 [&_input]:!text-sm">
                {productInvoice.map((product) => (
                  <tr key={product.idRow}>
                    <td className="!min-w-60 font-medium text-slate-700">
                      <SearchProduct
                        setProductInvoice={setProductInvoice}
                        product={product}
                        handleChangeProduct={handleChangeProduct}
                      />
                    </td>
                    <td>
                      <div className="flex items-center">
                        <button
                          className="inline-flex items-center justify-center p-1 me-3 text-sm font-medium h-6 w-6 text-gray-500 bg-white border border-gray-300 rounded-full focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200"
                          type="button"
                          onClick={() => updateQuantity(product.idRow, -1)}>
                          <span className="sr-only">Quantity button</span>
                          <svg
                            className="w-3 h-3"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 2">
                            <path
                              stroke="currentColor"
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              strokeWidth="2"
                              d="M1 1h16"
                            />
                          </svg>
                        </button>
                        <div>
                          <Input
                            name="quantity"
                            className="bg-white border-slate-500"
                            onChange={(e) =>
                              handleChangeProduct(e, product.idRow)
                            }
                            min={0}
                            required
                            type="number"
                            value={product.quantity}
                          />
                        </div>
                        <button
                          className="inline-flex items-center justify-center h-6 w-6 p-1 ms-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-full focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200"
                          type="button"
                          onClick={() => updateQuantity(product.idRow, +1)}>
                          <span className="sr-only">Quantity button</span>
                          <svg
                            className="w-3 h-3"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 18 18">
                            <path
                              stroke="currentColor"
                              strokeLinecap="round"
                              strokeLinejoin="round"
                              strokeWidth="2"
                              d="M9 1v16M1 9h16"
                            />
                          </svg>
                        </button>
                      </div>
                    </td>
                    <td>
                      <Input
                        name="unit"
                        className="bg-white border-slate-500"
                        onChange={(e) => handleChangeProduct(e, product.idRow)}
                        value={product.unit}
                        autoComplete="off"
                      />
                    </td>
                    <td>
                      <Input
                        type="number"
                        className="bg-white border-slate-500"
                        min={0}
                        name="price"
                        onChange={(e) => handleChangeProduct(e, product.idRow)}
                        value={product.price}
                        autoComplete="off"
                      />
                    </td>
                    <td>
                      <Input
                        type="number"
                        min={0}
                        className="!cursor-not-allowed bg-white border-slate-400 !text-slate-900"
                        disabled
                        value={product.amount}
                        autoComplete="off"
                      />
                    </td>
                    <td>
                      <Button
                        type="button"
                        variant="outline"
                        className="py-1.5"
                        onClick={(e) => deleteRow(product.idRow)}>
                        <Trash size={10} />
                      </Button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          <div className="">
            <Button
              type="button"
              variant="outline"
              className="py-1.5 mt-5 ml-6"
              onClick={addRow}>
              <Plus size={10} /> Tambah produk
            </Button>
          </div>
        </div>

        <div className="!col-span-full space-y-2 [&>div]:items-center [&>div]:grid [&>div]:grid-cols-2 [&>div]:md:grid-cols-12 [&>div>*]:md:col-span-4 [&>div>label]:md:col-start-5 [&>div>label]:lg:!col-start-7 [&>div>*]:lg:col-span-3 [&_label]:whitespace-nowrap [&_label]:pr-2 [&_label]:ml-auto [&_input]:border-slate-500">
          {/* sub_total */}
          <div>
            <Label htmlFor="sub_total">Sub Total : Rp</Label>
            <Input
              type="number"
              id="sub_total"
              disabled
              required
              value={data?.sub_total}
              autoComplete="off"
            />
          </div>

          {/* discount */}
          <div>
            <Label htmlFor="discount">Diskon : Rp</Label>
            <Input
              onChange={handleChange}
              type="number"
              id="discount"
              required
              value={data?.discount}
              autoComplete="off"
            />
          </div>

          {/* total */}
          <div>
            <Label htmlFor="total">Total : Rp</Label>
            <Input
              type="number"
              id="total"
              disabled
              required
              value={data?.total}
              autoComplete="off"
            />
          </div>

          {/* tax */}
          <div>
            <Label htmlFor="tax">Pajak 11% :</Label>
            <div className="flex items-center">
              <Checkbox
                id="tax"
                className="w-5 h-5 border-2 mr-2 border-slate-400"
                onCheckedChange={(value) => {
                  setData((prev) => ({
                    ...prev,
                    tax: value ? 1 : 0,
                  }));
                }}
              />
              <Label>Rp</Label>
              <Input
                type="number"
                disabled
                required
                value={data?.tax_price}
                autoComplete="off"
              />
            </div>
          </div>

          {/* grand_total */}
          <div>
            <Label htmlFor="grand_total">Grand Total : Rp</Label>
            <Input
              type="number"
              id="grand_total"
              disabled
              required
              value={data?.grand_total}
              autoComplete="off"
            />
          </div>

          {/* down_payment */}
          <div>
            <Label htmlFor="down_payment">DP : Rp</Label>
            <Input
              type="number"
              id="down_payment"
              onChange={handleChange}
              required
              value={data?.down_payment}
              autoComplete="off"
            />
          </div>

          {/* remaining_balance */}
          <div>
            <Label htmlFor="remaining_balance">Sisa : Rp</Label>
            <Input
              type="number"
              id="remaining_balance"
              disabled
              required
              value={data?.remaining_balance}
              autoComplete="off"
            />
          </div>
        </div>
      </FormDasboard>
    </Box>
  );
}
