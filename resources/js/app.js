import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link } from '@inertiajs/vue3';

createInertiaApp({
    title: (title) => title ? `${title} - PLN UP Sengkang` : 'Sistem Koordinasi UP Sengkang',
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const page = pages[`./Pages/${name}.vue`];
        if (!page) {
            console.error(`Page component not found: ./Pages/${name}.vue`);
        }
        return page;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .component('Link', Link)
            .component('Head', Head)
            .mount(el);
    },
    progress: {
        color: '#2563eb',
        showSpinner: true,
    },
});
