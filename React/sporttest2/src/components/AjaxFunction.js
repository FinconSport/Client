async function GetIni(apiUrl){
    return fetch(apiUrl, {method: 'POST',})
    .then((response)=>response.json())
    .then((responseJson)=>{return responseJson});
};


export default GetIni