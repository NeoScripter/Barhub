import { cn } from '@/lib/utils';
import { ComponentPropsWithoutRef, FC, ReactNode } from 'react';

type TableProps = ComponentPropsWithoutRef<'div'> & {
    children: ReactNode;
};

type TablePartProps = ComponentPropsWithoutRef<'table'> & {
    children: ReactNode;
};

type TableRowProps = ComponentPropsWithoutRef<'tr'> & {
    children: ReactNode;
};

type TableCellProps = ComponentPropsWithoutRef<'td'> & {
    children: ReactNode;
    width?: number;
};

type TableHeaderCellProps = ComponentPropsWithoutRef<'th'> & {
    children: ReactNode;
    width?: number;
};

function Table({ className, children, ...props }: TableProps) {
    return (
        <div
            className={cn('w-full overflow-x-auto', className)}
            {...props}
        >
            {children}
        </div>
    );
}

const TableHeader: FC<TablePartProps> = ({ className, children, ...props }) => {
    return (
        <table
            className={cn('w-full border-collapse bg-muted/10', className)}
            {...props}
        >
            <thead>{children}</thead>
        </table>
    );
};

const TableBody: FC<TablePartProps> = ({ className, children, ...props }) => {
    return (
        <table
            className={cn('w-full border-collapse', className)}
            {...props}
        >
            <tbody>{children}</tbody>
        </table>
    );
};

const TableRow: FC<TableRowProps> = ({ className, children, ...props }) => {
    return (
        <tr
            className={cn(
                'border-b border-gray-200 hover:bg-gray-50',
                className,
            )}
            {...props}
        >
            {children}
        </tr>
    );
};

const TableHeaderCell: FC<TableHeaderCellProps> = ({
    className,
    children,
    width = 1,
    ...props
}) => {
    return (
        <th
            className={cn(
                'px-4 py-3 text-left text-sm font-semibold text-secondary',
                className,
            )}
            style={{ width: `${width * 100}px` }}
            {...props}
        >
            {children}
        </th>
    );
};

const TableCell: FC<TableCellProps> = ({
    className,
    children,
    width = 1,
    ...props
}) => {
    return (
        <td
            className={cn('px-4 py-3 text-sm text-gray-600', className)}
            style={{ width: `${width * 100}px` }}
            {...props}
        >
            {children}
        </td>
    );
};

Table.Header = TableHeader;
Table.Body = TableBody;
Table.Row = TableRow;
Table.Cell = TableCell;
Table.HeaderCell = TableHeaderCell;

export default Table;
