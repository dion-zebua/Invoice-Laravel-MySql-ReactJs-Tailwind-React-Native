"use client";
import Box from "@/components/other/Box";
import fetch from "@/lib/fetch";
import { CheckCircle, XCircle } from "@deemlol/next-icons";
import React, { useEffect, useState } from "react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import SkeletonTable from "@/components/other/Table/SkeletonTable";
import { useSession } from "@/context/SessionContext";
import Link from "next/link";
import DataNotFound from "@/components/other/Table/DataNotFound";
import helper from "@/lib/helper";

export default function Invoice() {
  const user = useSession();
  const [isLoadingData, setIsLoadingData] = useState(true);
  const [data, setData] = useState(null);
  const [message, setMessage] = useState(null);

  const [params, setParams] = useState({
    page: 1,
    perPage: 5,
    orderBy: "id",
    orderDirection: "desc", // asc=oldest
  });

  useEffect(() => {
    setIsLoadingData(true);
    fetch
      .get("invoice", { params: params })
      .then((res) => setData(res.data.data))
      .catch((err) => {
        setData(null);
        let newMessage = err?.response?.data?.message ?? err?.message;
        if (newMessage && typeof newMessage == "object") {
          const messageFlat = Object.values(newMessage).flat();
          setMessage(
            <ul className="">
              {messageFlat.map((msg, index) => (
                <li key={index}>{msg}</li>
              ))}
            </ul>
          );
        } else {
          setMessage(newMessage);
        }
      })
      .finally(() => {
        setIsLoadingData(false);
      });
  }, [params]);

  return (
    <Box
      title="Invoice Terbaru"
      className="col-span-full h-80">
      <Table className="table-auto">
        <TableHeader>
          <TableRow className="bg-white hover:bg-white">
            <TableHead>Kode</TableHead>
            {user?.role == "admin" && <TableHead>Perusahaan Penjual</TableHead>}
            <TableHead>Perusahaan Pembeli</TableHead>
            <TableHead>Status</TableHead>
            <TableHead>Biaya</TableHead>
          </TableRow>
        </TableHeader>

        <TableBody>
          {/* Skelaton */}
          {isLoadingData && (
            <SkeletonTable
              column={user?.role == "user" ? 5 : 6}
              params={params}
            />
          )}

          {/* Data */}
          {!isLoadingData &&
            data?.data &&
            data?.data.length > 0 &&
            data?.data.map((col, i) => (
              <TableRow key={i}>
                <TableCell title={"#" + col["code"] ?? "-"}>
                  <Link
                    className="underline"
                    target="_blank"
                    href={`/invoice/${col?.id}/${col?.code}`}>
                    {"#" + col["code"] ?? "-"}
                  </Link>
                </TableCell>
                {user?.role == "admin" && (
                  <TableCell title={col?.user?.name ?? "-"}>
                    <Link
                      className="underline"
                      href={`/dashboard/pengguna/edit/${col?.user?.id}`}>
                      {col?.user?.name ?? "-"}
                    </Link>
                  </TableCell>
                )}
                <TableCell title={col?.to_name ?? "-"}>
                  {col?.to_name ?? "-"}
                </TableCell>
                <TableCell>
                  <div className="flex items-center gap-x-2 whitespace-nowrap">
                    {col?.status == "paid" ? (
                      <>
                        <CheckCircle
                          className="stroke-emerald-500"
                          size={15}
                        />
                        <span className="line-clamp-1 block text-ellipsis">
                          Lunas
                        </span>
                      </>
                    ) : (
                      <>
                        <XCircle
                          className="stroke-rose-500"
                          size={15}
                        />
                        <span className="line-clamp-1 block text-ellipsis">
                          Belum lunas
                        </span>
                      </>
                    )}
                  </div>
                </TableCell>
                <TableCell
                  title={helper.convertToRupiah(col?.grand_total ?? 0)}>
                  {helper.convertToRupiah(col?.grand_total ?? 0)}
                </TableCell>
              </TableRow>
            ))}

          {/* Not Found */}
          {!isLoadingData && !data?.data && (
            <DataNotFound
              column={7}
              message={message}
            />
          )}
        </TableBody>
      </Table>
    </Box>
  );
}
