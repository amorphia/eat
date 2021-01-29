import RestaurantList from "./views/RestaurantList";

export default {

    mode: 'history',
    linkActiveClass : 'active',

    routes: [
        {
            path: '*',
            component: RestaurantList,
            name: '404'
        },
        {
            path: '/',
            component: RestaurantList,
            name: 'home'
        },
    ]
}
