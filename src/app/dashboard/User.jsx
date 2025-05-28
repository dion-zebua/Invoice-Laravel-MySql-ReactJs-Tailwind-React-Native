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

export default function User() {
  const user = useSession();
  const [isLoadingData, setIsLoadingData] = useState(true);
  const [data, setData] = useState(null);
  const [message, setMessage] = useState(null);

  const [params, setParams] = useState({
    page: 1,
    perPage: 5,
    role: "user",
    orderBy: "id",
    orderDirection: "desc", // asc=oldest
  });

  useEffect(() => {
    setIsLoadingData(true);
    fetch
      .get("user", { params: params })
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
      title="User Terbaru"
      className="col-span-full h-80">
      <Table className="table-auto">
        <TableHeader>
          <TableRow className="bg-white hover:bg-white">
            <TableHead>Perusahaan</TableHead>
            <TableHead>Sales</TableHead>
            <TableHead>Telephone</TableHead>
            <TableHead>Status</TableHead>
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
                <TableCell title={col?.name ?? "-"}>
                  <Link
                    className="underline"
                    target="_blank"
                    href={`/dashboard/pengguna/edit/${col?.id}/`}>
                    {col?.name ?? "-"}
                  </Link>
                </TableCell>
                <TableCell title={col?.sales ?? "-"}>
                  {col?.sales ?? "-"}
                </TableCell>
                <TableCell title={col?.telephone ?? "-"}>
                  {col?.telephone}
                </TableCell>
                <TableCell>
                  <div className="flex items-center gap-x-2 whitespace-nowrap">
                    {col?.is_verified == 1 ? (
                      <>
                        <CheckCircle
                          className="stroke-emerald-500"
                          size={15}
                        />
                        <span className="line-clamp-1 block text-ellipsis">
                          Verif
                        </span>
                      </>
                    ) : (
                      <>
                        <XCircle
                          className="stroke-rose-500"
                          size={15}
                        />
                        <span className="line-clamp-1 block text-ellipsis">
                          Unverif
                        </span>
                      </>
                    )}
                  </div>
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
