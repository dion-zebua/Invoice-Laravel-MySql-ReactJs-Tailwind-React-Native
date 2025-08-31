import React from "react";
import Image from "next/image";
import helper from "@/lib/helper";

export default function Iframe(invoice) {
  const data = invoice.data;

  return (
    <div className="custom-container max-w-[640px] w-full">
      <div className="overflow-x-auto ">
        <div className="w-full min-w-[598px] border px-0 text-slate-500 [&_*]:!text-xs pb-4 [&_p]:line-clamp-1">
          {/* Status */}
          <div className="py-3 bg-blue-300 text-slate-50 flex justify-around italic">
            {Array.from({ length: 3 }).map((_, index) => (
              <p
                key={index}
                className="px-5">
                {data.status}
                {new Date(data.expire) < new Date() && ", expired"}
              </p>
            ))}
          </div>
          {/* Status */}

          {/* Date */}
          <div className="px-4">
            <div className="flex justify-between items-center py-4">
              <div className="">
                <Image
                  className="max-w-[110px] max-h-[70px]"
                  src={data.user.logo.result}
                  width={100}
                  height={25}
                  alt={"logo"}
                />
                <p className="mt-4">Invoice Generator</p>
              </div>
              <div className="flex flex-col text-right gap-y-3">
                <p className="font-semibold">#INV-{data.code}</p>
                <p>Order {helper.convertDate(data.order)}</p>
                <p>Expire {helper.convertDate(data.expire.split(" ")[0])}</p>
              </div>
            </div>
          </div>
          {/* Date */}

          {/* Profil */}
          <div className="px-4">
            <div className="flex justify-between items-center py-4">
              <div className="w-1/2 pr-10 flex gap-y-2 flex-col">
                <p className="pb-1 w-full font-semibold border-b inline-block">
                  Invoice From :
                </p>
                <p title={data.user.sales}>From : {data.user.sales}</p>
                <p title={data.user.name}>{data.user.name}</p>
                <p title={data.user.address}>{data.user.address}</p>
                <p title={data.user.email}>{data.user.email}</p>
                <p title={data.user.telephone}>{data.user.telephone}</p>
              </div>
              <div className="w-1/2 pl-10 text-right flex gap-y-2 flex-col">
                <p className="pb-1 w-full font-semibold border-b inline-block">
                  Invoice To :
                </p>
                <p title={data.to_sales}>To : {data.to_sales}</p>
                <p title={data.to_name}>{data.to_name}</p>
                <p title={data.to_address}>{data.to_address}</p>
                <p title={data.to_email}>{data.to_email}</p>
                <p title={data.to_telephone}>{data.to_telephone}</p>
              </div>
            </div>
          </div>
          {/* Profil */}

          {/* Produk */}
          <div className="px-4">
            <table className="w-full  border-gray-400 bg-white text-sm table-auto">
              <thead className="bg-gray-50 [&_th]:border [&_th]:border-gray-300 [&_th]:p-4 [&_th]:text-left [&_th]:font-semibold [&_th]:text-gray-900">
                <tr>
                  <th>No</th>
                  <th>Product</th>
                  <th>Qty</th>
                  <th>Unit</th>
                  <th>Price</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody className=" [&_td]:border [&_td]:border-gray-300 [&_td]:p-4 [&_td]:text-gray-500">
                {data.invoice_products.map((e, i) => {
                  return (
                    <tr key={i}>
                      <td>{i + 1}</td>
                      <td title={e.name}>{e.name}</td>
                      <td>{e.quantity}</td>
                      <td>{e.unit}</td>
                      <td>{helper.convertToRupiah(e.price)}</td>
                      <td>{helper.convertToRupiah(e.amount)}</td>
                    </tr>
                  );
                })}

                {/* Space */}
                <tr>
                  <td
                    className="!border-0"
                    colSpan={6}></td>
                </tr>
                {/* Space */}

                {/* Total Top */}
                <tr>
                  <td
                    rowSpan={3}
                    colSpan={4}>
                    <p className="font-semibold">Grand Total in Words:</p>
                    <p
                      title={data.numberToWords}
                      className="pl-5 pt-3 !line-clamp-none">
                      {data.numberToWords}
                    </p>
                  </td>
                  <td>Subtotal</td>
                  <td>{helper.convertToRupiah(data.sub_total)}</td>
                </tr>
                <tr>
                  <td>Discount</td>
                  <td>{helper.convertToRupiah(data.discount)}</td>
                </tr>
                <tr>
                  <td>Total</td>
                  <td>{helper.convertToRupiah(data.total)}</td>
                </tr>
                {/* Total Top */}

                {/* Total Bottom */}
                <tr>
                  <td
                    rowSpan={4}
                    colSpan={4}>
                    <p className="font-semibold"> Payment Methode:</p>
                    <p className="pl-5 pt-3 !line-clamp-none">
                      {data.user.payment_methode}
                    </p>
                    <p className="pl-5 pt-3 !line-clamp-none">
                      {data.user.payment_number}
                    </p>
                    <p className="pl-5 pt-3 !line-clamp-none">
                      {data.user.payment_name}
                    </p>
                  </td>
                  <td>Tax</td>
                  <td>
                    {helper.convertToRupiah(
                      data.tax == 1 ? (data.total * 11) / 100 : 0
                    )}
                  </td>
                </tr>
                <tr>
                  <td>Grand Total</td>
                  <td>{helper.convertToRupiah(data.grand_total)}</td>
                </tr>
                <tr>
                  <td>Down Payment</td>
                  <td>{helper.convertToRupiah(data.down_payment)}</td>
                </tr>
                <tr>
                  <td>Remaining Balance</td>
                  <td>{helper.convertToRupiah(data.remaining_balance)}</td>
                </tr>
                {/* Total Bottom */}
              </tbody>
            </table>
          </div>
          {/* Produk */}
        </div>
      </div>
    </div>
  );
}
