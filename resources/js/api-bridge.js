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
    },
}
