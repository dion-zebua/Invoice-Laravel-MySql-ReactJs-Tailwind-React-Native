import { TableCell, TableRow } from "@/components/ui/table";
import React from "react";

export default function DataNotFound(props) {
  const { column, message } = props;
  return (
    <TableRow>
      <TableCell
        colSpan={column}
        className="h-24 text-center [&>*]:max-w-full whitespace-normal">
        {message}
      </TableCell>
    </TableRow>
  );
}
