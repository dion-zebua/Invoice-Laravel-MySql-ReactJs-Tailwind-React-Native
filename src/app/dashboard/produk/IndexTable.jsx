"use client";

import Action from "@/components/other/Table/Action";
import DataNotFound from "@/components/other/Table/DataNotFound";
import Footer from "@/components/other/Table/Footer";
import Header from "@/components/other/Table/Header";
import SkeletonTable from "@/components/other/Table/SkeletonTable";
import { Button } from "@/components/ui/button";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import fetch from "@/lib/fetch";
import { CheckCircle, Edit, Trash, XCircle } from "@deemlol/next-icons";
import Link from "next/link";
import { useEffect, useState } from "react";
import error from "@/lib/error";
import { toast } from "sonner";
import Sortable from "@/components/other/Table/Sortable";
import Selector from "@/components/other/Table/Selector";
import { useSession } from "@/context/SessionContext";
import helper from "@/lib/helper";

export default function IndexTable() {
  const user = useSession();
  const [isLoadingData, setIsLoadingData] = useState(true);
  const [data, setData] = useState(null);
  const [message, setMessage] = useState(null);

  const [params, setParams] = useState({
    page: 1,
    perPage: 5,
    search: null,
    orderBy: "id",
    orderDirection: "desc", // asc=oldest
  });

  useEffect(() => {
    setIsLoadingData(true);
    fetch
      .get("product", { params: params })
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
      .finally(() => setIsLoadingData(false));
  }, [params]);

  const handleDelete = (e, col) => {
    e.preventDefault();
    toast.info("Sedang menghapus...");
    fetch
      .delete(`product/${col?.id}`)
      .then((response) => {
        setParams((prevData) => ({
          ...prevData,
          timeStamp: Date.now(),
        }));
        toast.success(response.data.message);
      })
      .catch((err) => {
        error(err);
      });
  };

  return (
    <>
      <Header
        isLoadingData={isLoadingData}
        params={params}
        setParams={setParams}
        searchColumn="produk, unit, harga"
      />

      <Table className="table-auto">
        <TableHeader>
          <TableRow className="bg-white hover:bg-white">
            <TableHead>
              <Sortable
                isLoadingData={isLoadingData}
                params={params}
                column="id"
                setParams={setParams}>
                No
              </Sortable>
            </TableHead>
            {user?.role == "admin" && (
              <TableHead>
                <Sortable
                  isLoadingData={isLoadingData}
                  params={params}
                  column="name"
                  setParams={setParams}>
                  Perusahaan Penjual
                </Sortable>
              </TableHead>
            )}
            <TableHead>
              <Sortable
                isLoadingData={isLoadingData}
                params={params}
                column="name"
                setParams={setParams}>
                Produk
              </Sortable>
            </TableHead>
            <TableHead>
              <Sortable
                isLoadingData={isLoadingData}
                params={params}
                column="unit"
                setParams={setParams}>
                Unit
              </Sortable>
            </TableHead>
            <TableHead>
              <Sortable
                isLoadingData={isLoadingData}
                params={params}
                column="price"
                setParams={setParams}>
                Harga
              </Sortable>
            </TableHead>
            <TableHead></TableHead>
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
                <TableCell>{i + data?.from}</TableCell>
                {user?.role == "admin" && (
                  <TableCell title={col?.user?.name ?? "-"}>
                    <Link
                      className="underline"
                      href={`./pengguna/edit/${col?.user?.id}`}>
                      {col?.user?.name ?? "-"}
                    </Link>
                  </TableCell>
                )}
                <TableCell title={col?.name ?? "-"}>
                  {col?.name ?? "-"}
                </TableCell>
                <TableCell title={col?.unit ?? "-"}>
                  {col?.unit ?? "-"}
                </TableCell>
                <TableCell title={helper.convertToRupiah(col?.price ?? 0)}>
                  {helper.convertToRupiah(col?.price ?? 0)}
                </TableCell>
                <TableCell>
                  <Action>
                    {/* Edit */}
                    {user?.role == "user" && (
                      <Link
                        href={`./produk/edit/${col?.id}/`}
                        className="block border-yellow-200 hover:bg-yellow-500 bg-yellow-700 p-1">
                        <Edit />
                      </Link>
                    )}

                    {/* Delete */}
                    <AlertDialog>
                      <AlertDialogTrigger
                        variant="destructive"
                        className="border-rose-200 hover:bg-rose-500 bg-rose-700">
                        <Trash />
                      </AlertDialogTrigger>
                      <AlertDialogContent>
                        <AlertDialogHeader>
                          <AlertDialogTitle>Anda yakin hapus?</AlertDialogTitle>
                          <AlertDialogDescription></AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                          <AlertDialogCancel>Tidak</AlertDialogCancel>
                          <form onSubmit={(e) => handleDelete(e, col)}>
                            <AlertDialogAction type="submit">
                              Ya
                            </AlertDialogAction>
                          </form>
                        </AlertDialogFooter>
                      </AlertDialogContent>
                    </AlertDialog>
                  </Action>
                </TableCell>
              </TableRow>
            ))}

          {/* Not Found */}
          {!isLoadingData && !data?.data && (
            <DataNotFound
              column={user?.role == "user" ? 5 : 6}
              message={message}
            />
          )}
        </TableBody>
      </Table>

      <Footer
        setParams={setParams}
        data={data}
      />
    </>
  );
}
