<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Axios</title>
</head>
<body>
<button onclick="getToken()">取消请求</button>
<button onclick="getUser()">获取用户信息</button>
</body>
<script src="//cdn.bootcss.com/axios/0.16.1/axios.min.js"></script>
<script src="http://cdn.bootcss.com/crypto-js/3.1.9/crypto-js.min.js"></script>
<script>
    var CancelToken = axios.CancelToken;
    var source = CancelToken.source();

    axios.get('/application/api/search/12345', {
        cancelToken: source.token,
        params:{index :1, size: 10}
    }).catch(function(thrown) {
        if (axios.isCancel(thrown)) {
            console.log('Request canceled', thrown.message);
        } else {
            // handle error
        }
    });
    function getToken() {
        source.cancel('Operation canceled by the user.');
    }
    // cancel the request (the message parameter is optional)
</script>
</html>