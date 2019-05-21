import axios from 'axios';

let axiosInstance = axios.create({
    baseURL: 'api/v1',
    headers: {
        "Content-Type": "application/json",
    }
});

function parseUrl(url, params) {
    for (let key in params) {
        let param = params[key];
        url = url.replace(':' + key, param);
    }

    return url;
}

export default {

    books: {
        getAll: function (params) {
            return axiosInstance.get('/books', {
                params: params,
            });
        },

        'delete': function (id) {
            let url = parseUrl('books/:id', {id});
            return axiosInstance.delete(url);
        },

        update: function (id, params) {
            let url = parseUrl('books/:id', {id});
            return axiosInstance.put(url, params);
        },

        create: function (params) {
            return axiosInstance.post('books', params);
        },

        'export': function (type, params) {
            let url = parseUrl('books/export/:type', {type});
            return axiosInstance.get(url, {
                params,
                responseType: 'blob',
            });
        }
    },
}
