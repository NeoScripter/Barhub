import type { SVGAttributes } from 'react';

export default function BurgerMenuIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg
            {...props}
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
        >
            <path
                d="M24 11.001H0V13.001H24V11.001Z"
                fill="#ED4B97"
            />
            <path
                d="M24 4.00049H0V6.00048H24V4.00049Z"
                fill="#ED4B97"
            />
            <path
                d="M24 18H0V20H24V18Z"
                fill="#ED4B97"
            />
        </svg>
    );
}
