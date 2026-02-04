import { createInertiaApp } from '@inertiajs/react';
import createServer from '@inertiajs/react/server';
import ReactDOMServer from 'react-dom/server';
import UserLayout from './layouts/app/UserLayout';
import AdminLayout from './layouts/app/AdminLayout';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const pages = import.meta.glob('./pages/**/*.tsx', { eager: true });

createServer((page) =>
    createInertiaApp({
        page,
        render: ReactDOMServer.renderToString,
        title: (title) => (title ? `${title} - ${appName}` : appName),
        resolve: (name) => {
            const page: any = pages[`./pages/${name}.tsx`];

            const Layout = name.startsWith('user/') ? UserLayout : AdminLayout;

            page.default.layout ??= (p: React.ReactNode) => (
                <Layout>{p}</Layout>
            );

            return page;
        },

        setup: ({ App, props }) => {
            return <App {...props} />;
        },
    }),
);
