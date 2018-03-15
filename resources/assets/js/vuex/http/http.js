/**
 * @description defined your domain of api
 */
export const API_DOMAIN = ''
/**
 * @description generate header when create request
 */
function generateHeader() {
    let headers = {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    return headers
}
/**
 * @description http method post using axios
 * 
 * @export
 * @param {any} uri 
 * @param {any} params 
 * @returns 
 */
export function post(uri, params) {
    return new Promise((resolve, reject) => {
        window.axios.post(uri, params, {
            headers: generateHeader()
        })
            .then((response) => {
                resolve(response)
            })
            .catch(ex => {
            });
    })
}

/**
 * @description http method get using axios
 * 
 * @export
 * @param {any} uri 
 * @param {any} params 
 * @returns 
 */
export function get(uri, params) {
    return new Promise((resolve, reject) => {
        window.axios.get(uri, params, {
            headers: generateHeader()
        })
            .then((response) => {
                resolve(response)
            })
            .catch(ex => {
                
            });
    })
}
/**
 * @description the way that using query string to get data from api
 * 
 * @export
 * @param {any} uri 
 * @returns 
 */
export function queryString(uri) {
    return new Promise((resolve, reject) => {
        window.axios.get(uri, {
            headers: generateHeader()
        })
            .then((response) => {
                resolve(response)
            })
            .catch(ex => {
                
            });
    })
}

