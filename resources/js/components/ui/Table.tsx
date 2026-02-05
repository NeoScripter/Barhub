import { cn } from '@/lib/utils';
import { ComponentPropsWithoutRef, FC, ReactNode } from 'react';

type TableProps = ComponentPropsWithoutRef<'table'> & {
    children: ReactNode;
};

type TablePartProps = ComponentPropsWithoutRef<'thead' | 'tbody'> & {
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
        <div className="w-full overflow-x-auto pb-4">
            <table
                className={cn('w-full border-collapse', className)}
                {...props}
            >
                {children}
            </table>
        </div>
    );
}

const TableHeader: FC<TablePartProps> = ({ className, children, ...props }) => {
    return (
        <thead
            className={cn('bg-muted/10', className)}
            {...props}
        >
            {children}
        </thead>
    );
};

const TableBody: FC<TablePartProps> = ({ className, children, ...props }) => {
    return (
        <tbody
            className={className}
            {...props}
        >
            {children}
        </tbody>
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
                'min-w-30 px-4 py-3 text-left text-sm font-semibold text-secondary 2xl:text-base',
                className,
            )}
            style={{ minWidth: `${width * 120}px` }}
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
            className={cn(
                'min-w-30 px-4 py-3 text-sm text-gray-600 2xl:text-base',
                className,
            )}
            style={{ minWidth: `${width * 120}px` }}
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
