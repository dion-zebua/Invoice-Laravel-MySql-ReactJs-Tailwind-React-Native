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

export default function IndexTable() {
  const [isLoadingData, setIsLoadingData] = useState(true);
  const [data, setData] = useState(null);
  const [message, setMessage] = useState(null);

  const [params, setParams] = useState({
    page: 1,
    perPage: 5,
    is_verified: null,
    search: null,
    role: null,
    orderBy: "id",
    orderDirection: "asc", // asc=oldest
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
      .finally(() => setIsLoadingData(false));
  }, [params]);

  const handleDelete = (e, col) => {
    e.preventDefault();
    toast.info("Sedang menghapus...");
    fetch
      .delete(`user/${col?.id}`)
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
        searchColumn="name, sales, telephone"
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
            <TableHead>
              <Sortable
                isLoadingData={isLoadingData}
                params={params}
                column="name"
                setParams={setParams}>
                Perusahaan
              </Sortable>
            </TableHead>
            <TableHead>
              <Sortable
                isLoadingData={isLoadingData}
                params={params}
                column="sales"
                setParams={setParams}>
                Sales
              </Sortable>
            </TableHead>
            <TableHead>
              <Selector
                isLoadingData={isLoadingData}
                params={params}
                column="role"
                setParams={setParams}
                item={[
                  { key: null, label: "semua" },
                  { key: "admin", label: "admin" },
                  { key: "user", label: "user" },
                ]}>
                Role
              </Selector>
            </TableHead>
            <TableHead>Telephone</TableHead>
            <TableHead>
              <Selector
                isLoadingData={isLoadingData}
                params={params}
                column="is_verified"
                setParams={setParams}
                item={[
                  { key: null, label: "semua" },
                  { key: 1, label: "verif" },
                  { key: 0, label: "unverif" },
                ]}>
                Status
              </Selector>
            </TableHead>
            <TableHead></TableHead>
          </TableRow>
        </TableHeader>

        <TableBody>
          {/* Skelaton */}
          {isLoadingData && (
            <SkeletonTable
              column={7}
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
                <TableCell title={col?.name ?? "-"}>{col?.name ?? "-"}</TableCell>
                <TableCell title={col?.sales ?? "-"}>{col?.sales ?? "-"}</TableCell>
                <TableCell>
                  <div
                    className={`inline-block font-medium border-2 text-slate-100 px-2 py-0.1 rounded-full ${
                      col?.role == "user"
                        ? "bg-violet-700 border-violet-200"
                        : "bg-teal-700 border-teal-200"
                    }`}>
                    {col?.role}
                  </div>
                </TableCell>
                <TableCell title={col?.telephone ?? "-"}>{col?.telephone ?? "-"}</TableCell>
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
                <TableCell>
                  <Action>
                    {/* Edit */}
                    <Link
                      href={`./pengguna/edit/${col?.id}/`}
                      className="block border-yellow-200 hover:bg-yellow-500 bg-yellow-700 p-1">
                      <Edit />
                    </Link>

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
              column={7}
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
