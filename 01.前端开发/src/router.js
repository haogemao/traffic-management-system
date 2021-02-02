const publicRoutes = [
    {
        name: 'login',
        path: '/login',
        meta: {
            requireAuth: false,
        },
        component: (resolve) => require(['./views/login/login.vue'], resolve)
    },
    {
        name: 'manage',
        path: '/',
        redirect: 'person',
        meta: {
            requireAuth: true,
        },
        component: (resolve) => require(['./views/layout/BasicLayout.vue'], resolve),
        children: [
            {
                name: 'person',
                path: 'person',
                meta: {
                    title: 'Person',
                    isNav: true,
                    requireAuth: true,
                },
                component: (resolve) => require(['./views/person/person.vue'], resolve)
            },
            {
                name: 'vehicle',
                path: 'vehicle',
                meta: {
                    title: 'Vehicle',
                    isNav: true,
                    requireAuth: true,
                },
                component: (resolve) => require(['./views/vehicle/vehicle.vue'], resolve)
            },
            {
                name: 'report',
                path: 'report',
                meta: {
                    title: 'Report',
                    isNav: true,
                    requireAuth: true,
                },
                component: (resolve) => require(['./views/report/report.vue'], resolve),
            },
            {
                name: 'advance',
                path: 'advance',
                meta: {
                    title: 'Advance',
                    isNav: true,
                    requireAuth: true,
                },
                component: (resolve) => require(['./views/advance/advance.vue'], resolve),
            },
        ]
    },
];



export default publicRoutes;
