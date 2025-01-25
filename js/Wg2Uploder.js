class Wg2Uploder{

  static getUploadUrl(){
    const scriptNode = globalThis.document.querySelector('script[src $= "Wg2Uploder.js"]')
    const path = scriptNode.src
    const uploadUrl = path.replace(/\\/g, '/').replace(/\/js\/Wg2Uploder\.js$/, '/up.php');
    // console.log(uploadUrl);
    return uploadUrl;
  }
  static upload(file,filename){

    const data = new FormData;
    data.append('upf',file,filename);
    data.append('callback','json');
    const url = this.getUploadUrl();
    // console.log(url);
    
    return fetch(url,
      {
        method: data?'POST':'GET', // *GET, POST, PUT, DELETE, etc.
        // mode: 'same-origin', // no-cors, cors, *same-origin
        // cache: 'default', // *default, no-cache, reload, force-cache, only-if-cached
        // credentials: 'same-origin', // include, *same-origin, omit
        headers: {
          // 'Content-Type': 'application/json',
          // 'Content-Type': 'application/x-www-form-urlencoded',
          // 'Content-Type': 'multipart/form-data', // 첨부파일 업로드인 경우
        },
        // redirect: 'follow', // manual, *follow, error
        // referrer: 'client', // no-referrer, *client
        // referrerPolicy:'', // empty-string, "no-referrer", "no-referrer-when-downgrade", "same-origin", "origin", "strict-origin", "origin-when-cross-origin", "strict-origin-when-cross-origin", or "unsafe-url".
        // integrity:'',
        // keepalive:''
        //(new AbortController()).signal, //signal //실험적 기능. 사용하지 말자.
        // body: bodyData, // body data type must match "Content-Type" header //GET, HEAD 인 경우 body가 있으면 안된다.
        body: data, //  Blob, BufferSource, FormData, URLSearchParams, USVString, or ReadableStream // body data type must match "Content-Type" header //GET, HEAD 인 경우 body가 있으면 안된다.
      })
      .then(function(response){
        // console.log("response",response)
        // console.log("headers")
        // for(const kv of response.headers.entries()){
        //   console.log(kv);
        // }
        if(!response.ok){
          throw new Error(`HTTP error! status: ${response.status}`); // http 응답 오류부
        }else{
          // OK
        }
        return response.json();  // response.arrayBuffer() .blob() .formData() .json() .text()
      })
      // .then(function(text){
      //   console.log(text)
      // })
      // .catch(function(error){
      //   console.error(error);
      // })
  }
}