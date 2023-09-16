# 個人商城


收單端接口

一律以POST格式發送


### 注冊  /api/register

Request 

```javascript
{
  "email" : "EMAIL@DOMAIN",
  "password" : "123456"
}
```

Reponse 

```javascript
{
    "status" : 1
    "data" : {
        "id" : 123456,
        "email" : "email",
        "token" : "1234567qweqefdjisaodujasio"
    },
    "message:" : "success message"
}
```


### 判斷買家是否登入  /api/isLogin

Request 

```javascript
{
  "email" : "EMAIL@DOMAIN",
  "token" : "1234567qweqefdjisaodujasio"
}
```

Reponse 

```javascript
{
    "status" : 1
    "data" : [
      "seller_type":0 // 0:未申請 , 1:審核中 , 2: 個人賣家 , 3:企業賣家
    ],
    "message:" : "success message"
}
```

### 登入  /api/login

Request 

```javascript
{
  "email" : "EMAIL@DOMAIN",
  "password" : "123456"
}
```

Reponse 

```javascript
{
    "status" : 1
    "data" : {
        "id" : 123456,
        "email" : "email",
        "token" : "1234567qweqefdjisaodujasio"
    },
    "message:" : "success message"
}
```


### 登出  /api/logout

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio"
}
```

Reponse 

```javascript
{
    "status" : 1
    "data" : [],
    "message:" : "success message"
}
```


### 修改買家密碼  /api/changePassword

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "old_password" : "12345", // 舊密碼
  "new_password" : "12345", // 新密碼
  "confirm_password" : "12345" // 再次確認密碼
}
```

Reponse 

```javascript
{
    "status" : 1
    "data" : [],
    "message:" : "success message"
}
```


### 取得分類  /api/getCatalog

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "seller_id":1, // 賣家id
}
```

Reponse 

```javascript
{
  "status":1,
  "data":[
    {
      "id":1,
      "seller_id":1,
      "catalog_name":"分類1",
      "catalog_picture":"分類背景網址",
      "order_by":1,
      "status":1
    },
    {
      "id":2,
      "seller_id":1,
      "catalog_name":"分類2",
      "catalog_picture":"分類背景網址",
      "order_by":2,
      "status":1
    },
    {
      "id":3,
      "seller_id":1,
      "catalog_name":"分類3",
      "catalog_picture":"分類背景網址",
      "order_by":3,
      "status":1
    }
  ],
  "message":"ajax.success_getcatalog_01"
}
```


### 取得商品  /api/getProduct

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "seller_id":1, // 賣家id
  "catalog_id":1, // 不帶參數為全部分類
  "shop_catalog_id":1, // 不帶參數為全部分類
  "page":1, // 不帶參數為第一頁
}
```

Reponse 

```javascript
{
  "status":1,
  "data":[
    {
      "id":1,  // product id
      "seller_id":1, // 賣場 id 
      "catalog_id":1,  // 分類id 
      "catalog_name":"A", // 分類名稱
      "product_name":"DELL27\u578bUltraSharp2KIPS\u7a84\u908a\u6846\u7f8e\u578b\u87a2\u5e55U2722D", // 商品名稱
      "product_sku":"skuA", // 商品編號
      "product_option":["G","H","I","J","K","L","M","N"], // 商品規格-選項
      "product_description":"", // 商品描述
      "product_picture":"../assets/images/noimg.jpg", // 商品圖片
      "product_prices":1000,  // 商品價格
      "product_quantity":30, // 商品庫存
      "product_sold":0,  // 商品已售出
      "product_rate":4, // 商品評價 (星)
      "status":1  // 狀態
    }
  ],
  "message":"ajax.success_getproduct_01"
}
```

### 取得購物車數量  /api/getCartCount

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
}
```

Reponse 

```javascript
{
  "status":1,
  "data": 5, // 未登入為0 , 登入後則回傳該用戶的購物車商品數
  "message":"ajax.success_getproduct_01"
}
```

### 取得商品詳情  /api/getProductDetail


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id" : 1 // 必填,  商品ID
}
```

Reponse 

```javascript
{
  "status":1,
  "data":{
      "id":1,  // product id
      "seller_id":1, // 賣場 id 
      "catalog_id":1,  // 分類id 
      "catalog_name":"A", // 分類名稱
      "product_name":"DELL27\u578bUltraSharp2KIPS\u7a84\u908a\u6846\u7f8e\u578b\u87a2\u5e55U2722D", // 商品名稱
      "product_sku":"skuA", // 商品編號
      "product_option":["G","H","I","J","K","L","M","N"], // 商品規格-選項
      "product_description":"", // 商品描述
      "product_picture":"../assets/images/noimg.jpg", // 商品圖片
      "product_prices":1000,  // 商品價格
      "product_quantity":30, // 商品庫存
      "product_sold":0,  // 商品已售出
      "product_rate":4, // 商品評價 (星)
      "status":1,  // 狀態
      "product_picture": [ // 商品圖片, 多組
          "http://img.shopping168.net/ed0eabed6f04173465a8b12207d2320a.jpg",
          "http://img.shopping168.net/f5bdc21804a3eb1813183f6dc7c8b706.jpg",
          "http://img.shopping168.net/4ab8404271a6188ca7e424d06166ca8a.jpg",
          "http://img.shopping168.net/1e9a8ab2d37c975364858ce358813ed6.jpg",
          "http://img.shopping168.net/873bedb97d4570dfc3eac7d1c8a9f222.jpg"
      ]
  },
  "message":"ajax.success_getproductdatil_01"
}
```

### 增加商品評論  /api/addComment


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id" : 1, // 必填,  購物訂單ID
  "title" : "標題", // 評論標題
  "comment" : "內文", // 評論內文
  "rate" : 5, // 星數 1-5
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[],
  "message":"ajax.success_addComment_01"
}
```

### 取得商品評論  /api/getComment

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "product_id" : 1 // 必填,  商品ID
}
```

Reponse 

```javascript
{
  "status":1,
  "data":{
    "id":1, // 評價ID 
    "seller_id":1, // 賣場ID
    "product_id":1, // 商品ID
    "name":"\u6e2c\u8a661", // 評價標題
    "email":"1@2.3", // 評價者, email
    "rate":5, // 評價星數
    "comment":"1234567890",  // 評價內文
    "create_time":"2021-09-23 15:16:01", // 發表時間
    "status":1
  },
  "message":"success message"
}
```

### 加入購物車  /api/insertCart

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "product_id" : 1, // 必填,  商品ID
  "product_option" : "黑色", // 商品規格, 字段
  "quantity" : 1 // 必填,  購買數量
}
```

Reponse 

```javascript
{
  "status":1,
  "data":2, // 購物車 商品數量
  "message":"success message"
}
```

### 立即購買  /api/purchaseCart

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "product_id" : 1, // 必填,  商品ID
  "product_option" : "黑色", // 商品規格, 字段
  "quantity" : 1 // 必填,  購買數量
}
```

Reponse 

```javascript
{
  "status":1, // 成功後 跳轉購物車頁
  "data":2, // 購物車 商品數量
  "message":"success message"
}
```

### 取得購物車  /api/getCart

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
}
```

Reponse 

```javascript
{
  "status":1,
  "data":{
        "1": {
            "shop": {
                "seller_id": 1,
                "shop_name": "測試商店",
                "shop_logo": ""
            },
            "items": [
                {
                    "id": 1189,
                    "seller_id": 1,
                    "buyer_id": 1,
                    "product_id": 1,
                    "product_name": "aaa",
                    "product_option": "d",
                    "product_quantity": 1,
                    "product_prices": 100,
                    "shipping_fee": 0,
                    "status": 1,
                    "product_picture": "https://img.shopping166.net/a4e662a470e16f6442d96e05853da9e4.jpg",
                    "current_quantity": 111
                }
            ]
        },
        "17": {
            "shop": {
                "seller_id": 17,
                "shop_name": "格木家具城店鋪",
                "shop_logo": ""
            },
            "items": [
                {
                    "id": 188,
                    "seller_id": 17,
                    "buyer_id": 1,
                    "product_id": 25,
                    "product_name": "公香魚(六尾裝)",
                    "product_option": "920",
                    "product_quantity": 1,
                    "product_prices": 650,
                    "shipping_fee": 0,
                    "status": 1,
                    "product_picture": "../../assets/images/noimg.jpg",
                    "current_quantity": 15
                },
                {
                    "id": 189,
                    "seller_id": 17,
                    "buyer_id": 1,
                    "product_id": 25,
                    "product_name": "公香魚(六尾裝)",
                    "product_option": "920",
                    "product_quantity": 1,
                    "product_prices": 650,
                    "shipping_fee": 0,
                    "status": 1,
                    "product_picture": "../../assets/images/noimg.jpg",
                    "current_quantity": 15
                }
            ]
        },
    },
  "message":"success message"
}
```

### 變更購物車商品數量  /api/changeCartQuantity

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id" : 1, // 必填,  購物車ID
  "product_quantity" : 1, // 必填,  要更新的商品數量
}
```

Reponse 

```javascript
{
  "status":1,
  "data": null,
  "message":"success message"
}
```


### 移除購物車商品  /api/removeCart

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id" : 1, // 必填,  購物車ID
}
```

Reponse 

```javascript
{
  "status":1,
  "data": [],
  "message":"success message"
}
```

### 取得物流方式  /api/getShipping

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio"
}
```

Reponse 

```javascript
{
  "status":1,
  "data": [
       {
            "shipping_id": 1, // 物流ID
            "name": "蝦拼快遞", // 物流名稱
            "payment": [  // 支付array
                {
                    "id": 1,  // 支付ID
                    "name": "貨到付款" // 支付名稱
                },
                {
                    "id": 2,
                    "name": "信用卡"
                },
                {
                    "id": 3,
                    "name": "LINEPAY"
                }
            ]
        },
  ],
  "message":"success message"
}
```

### 取得支付方式  /api/getPayment

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id" : 1, // 必填,  購物車ID
  "product_quantity" : 1, // 必填,  要更新的商品數量
}
```

Reponse 

```javascript
{
  "status":1,
  "data": {
    "1":"\u8766\u62fc\u5feb\u905e",  // 支付ID :支付顯示名稱
    "2":"7-11\u53d6\u8ca8",
    "3":"\u9ed1\u8c93\u5b85\u914d"
  },
  "message":"success message"
}
```

### 提交訂單  /api/createOrder

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "shipping" : 1, // ShippingID
  "payment" : 2, // paymentID
  "items" : "[1,2,3,4,5]", // 商品array , Cart ID
  "addressName" : "某某某", // 收件人
  "addressPhone" : "0912345678", // 收件人手機
  "addressCity" : "台北市", // 都市
  "addressArea" : "東區", // 區
  "addressAddress" : "1234567qweqefdjisaodujasio", // 地址
  "addressZip" : 100, // 郵遞區號
}
```

Reponse 

```javascript
{
  "status":1,
  "data": [12345,23456],  // 訂單ID
  "message":"success message"
}
```

### 取得訂單詳情  /api/getOrder
```
已取消
```

### 取得訂單詳情  /api/getShopOrder


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "status":"1", // 不帶預設為全部, 需登入
  // 0: 取消 , 1:待付 ,2:待出貨 , 3:待收件 , 4:已完成
  "page":1, // 不帶參數為第一頁
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[
  {
    "id":1, // 訂單ID
    "seller_id":1, // 賣場ID
    "buyer_id":1, // 買家ID
    "shipping":-1, // 物流方式ID
    "payment":-1, // 支付方式id
    "prices":5000, // 訂單金額
    "data":[{   // 訂單商品 , 多個
      "id":3, // 購物車ID
      "seller_id":1, // 賣家ID
      "buyer_id":1, // 買家ID
      "product_id":1, // 商品ID 
      "product_name":"DELL27\u578bUltraSharp2KIPS\u7a84\u908a\u6846\u7f8e\u578b\u87a2\u5e55U2722D",
      "product_option":"A", // 商品規格
      "product_quantity":2, // 購買數量
      "product_prices":1000, // 小計金額
      "status":1
      }],
    "create_time":"2021-09-30 04:39:38",
    "shop_id": 1, // 店鋪ID
    "shop_name": "測試商店", //店鋪名稱
    "shop_logo": "", // 店鋪LOGO
    "status":2 // 訂單狀態
  }],
  "message":"ajax.success_getorder_01"
}
```


### 取得退貨單詳情  /api/getReturnOrder
已取消

### 取得退貨單詳情  /api/getShopReturnOrder


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "page":1, // 不帶參數為第一頁
  "status":"1" // 不帶預設為全部
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[
        {
            "id": 1,  // 退貨單ID
            "seller_id": 1,
            "buyer_id": 1,
            "shop_order_id": 1, // 購物訂單
            "shipping_order": "55555",  // 買家退貨 物流單
            "items": "",
            "create_time": "2021-11-16 15:58:49", // 創建時間
            "shop_id": 1, // 店鋪ID
            "shop_name": "測試商店", //店鋪名稱
            "shop_logo": "", // 店鋪LOGO
            "status": 4,  // 退貨狀態
            "status_name": "已退貨",  // 狀態名稱
            "data": [   // 退貨商品資料
                {
                    "id": 20,
                    "seller_id": 1,
                    "buyer_id": 1,
                    "product_id": 1,
                    "product_name": "LOGIS｜雙色電腦椅 網布升級 透氣椅 全網椅 辦公椅 主管椅 台灣製 椅子 書桌椅 人體工學椅",
                    "product_option": "黑",
                    "product_quantity": 1,
                    "product_prices": 4000,
                    "shipping_fee": 0,
                    "status": 1,
                    "product_picture": "../../assets/images/noimg.jpg"
                }
            ]
        }
  ],
  "message":"ajax.success_getReturnOrder_01"
}
```


### 取得訂單分類  /api/getOrderCatalog


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[
        {
            "status": 0,  // 狀態值
            "name": "取消", // 狀態名稱
            "count": 0      // 統計
        },
        {
            "status": 1,
            "name": "待付款",
            "count": 7
        },
        {
            "status": 2,
            "name": "待出貨",
            "count": 2
        },
        {
            "status": 3,
            "name": "派送中",
            "count": 0
        },
        {
            "status": 4,
            "name": "待收件",
            "count": 0
        },
        {
            "status": 5,
            "name": "待確認 ",
            "count": 0
        },
        {
            "status": 6,
            "name": "完成 ",
            "count": 0
        },
        {
            "status": 7,
            "name": "退單 ",
            "count": 0
        }],
  "message":"ajax.success_getOrderCatalog_01"
}
```

### 取得退貨單分類/統計  /api/getReturnOrderCatalog


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[
        {
            "status": -1,
            "name": "全部",
            "count": 1
        },
        {
            "status": 0,
            "name": "取消退貨",
            "count": 0
        },
        {
            "status": 1,
            "name": "等待賣家確認",
            "count": 0
        },
        {
            "status": 2,
            "name": "等待商品退回",
            "count": 0
        },
        {
            "status": 3,
            "name": "賣家商品確認中",
            "count": 0
        },
        {
            "status": 4,
            "name": "已退貨",
            "count": 1
        }
      ],
  "message":"ajax.success_getReturnOrderCatalog_01"
}
```

### 取得SEO設定,動態標提  /api/getSeo


Request 

```javascript
{
  "product_id":"1" // 不帶預設為全部, 不需登入
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":{
    "shop_title":"標題標題標題標題", // 標題
    "shop_keywords": "keywords", // keywords
    "shop_description": "description", // description
    "shop_icon": "/assets/img/icon.png", // icon
    "shop_logo": "/assets/img/logo.png", // logo
  },
  "message":"ajax.success_getseo_01"
}
```

### 付款完成,提交接口  /api/submitPayment


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id" : 12345  // 購物訂單ID , 暫定
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[],
  "message":"ajax.success_submitPayment_01"
}
```

### 買家提交完成訂單  /api/finishOrder


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id" : 12345  // 購物訂單ID , 暫定
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[],
  "message":"ajax.success_finishOrder_01"
}
```

### 買家新增常用地址  /api/addAddress


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
	"revicer_name" : "收件人名稱",
	"revicer_phone" : "收件人電話",
	"city" : "台北市",
	"zone" : "東區",
	"address" : "基隆路1段1234號",
	"zipcode" : "10010"
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[],
  "message":"ajax.success_addAddress_01"
}
```

### 買家刪除常用地址  /api/removeAddress


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
	"id" : 1 // 地址id
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[],
  "message":"ajax.success_removeAddress_01"
}
```

### 買家取得常用地址  /api/getAddress


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio"
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[
    {
      "id" : 1,
      "revicer_name" : "收件人名稱",
      "revicer_phone" : "收件人電話",
      "city" : "台北市",
      "zone" : "東區",
      "address" : "基隆路1段1234號",
      "zipcode" : "10010"
    },
    {
      "id" : 2,
      "revicer_name" : "收件人名稱",
      "revicer_phone" : "收件人電話",
      "city" : "台北市",
      "zone" : "東區",
      "address" : "基隆路1段1234號",
      "zipcode" : "10010"
    }
  ],
  "message":"ajax.success_removeAddress_01"
}
```


### 買家創建退貨單  /api/createReturnOrder


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id" : 1 // 購物訂單ID
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[],
  "message":"ajax.success_createReturnOrder_01"
}
```

### 買家提交退貨物流單號  /api/shippingReturnOrder


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id" : 1, // 退貨單ID
  "shipping_order" : "ABC123456" // 物流單號
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[],
  "message":"ajax.success_shippingReturnOrder_01"
}
```

### 買家搜尋商品  /api/searchProduct

已取消

### 買家搜尋商品  /api/searchShopProduct


已取消

### 買家提出賣家申請  /api/createSeller


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "buyer_name" : "真實姓名",
  "buyer_birthday" : "2022-01-01",
  "buyer_idcard" : "身份證",
  "buyer_agree" : 1,
}
```

Reponse 

```javascript
{
    "status": 1,
    "data": [],
    "message": "ajax.success_createSeller_01"
}
```
### 通用商城分類  /api/getShopCatalog


Request 

```javascript
{
}
```

Reponse 

```javascript
{
    "status": 1,
    "data": [
        {
            "id": 0,
            "catalog_name": "所有商品",
            "children":[
                {
                    "id": 0,
                    "catalog_name": "所有商品"
                },
                {
                    "id": 1,
                    "catalog_name": "女生衣著"
                },
                {
                    "id": 19,
                    "catalog_name": "美食、伴手禮"
                }
              ],
        }
    ], 
    "message": "ajax.success_getShopCatalog_01"
}
```


### 取得通用商城商品  /api/getShopProduct

Request 

```javascript
{
  "shop_catalog_parent_id":1, // 父分類, 不帶參數為全部分類
  "shop_catalog_id":1, // 子分類,不帶參數為全部分類
  "product_name" : "FU", // 搜尋產品名稱
  "catalog_id": "1,2,3",  // 多個分類, 以,分隔
  "prices": "10,10000",  // 價格區間, 以,分隔 , 從最小到最大值
  "rate" : "5", // 評比 , 大於等於
  "order_by" : "product_quantity,DESC",
  // 上架 ： create_time
  // 銷售 ： product_sold 
  // 庫存 : product_quantity
  // 從小到大 : ASC
  // 從大到小 : DESC
  "page":1, // 不帶參數為第一頁
}└
```

Reponse 

```javascript
{
  "status":1,
  "data":[
    {
      "id":1,  // product id
      "seller_id":1, // 賣場 id 
      "shop_catalog_parent_id": 1,// 通用分類父層id 
      "shop_catalog_parent_name": "父層分類",// 通用分類父層名稱 
      "shop_catalog_id": 2, // 通用分類子層id
      "shop_catalog_name":  "子層分類", // 通用分類子層名稱 
      "shop_catalog_tree": ",1,2,", // 通用分類樹 
      "catalog_id":1,  // 標籤id 
      "catalog_name":"A", // 標籤名稱
      "product_name":"DELL27\u578bUltraSharp2KIPS\u7a84\u908a\u6846\u7f8e\u578b\u87a2\u5e55U2722D", // 商品名稱
      "product_sku":"skuA", // 商品編號
      "product_option":["G","H","I","J","K","L","M","N"], // 商品規格-選項
      "product_picture":"NOIMG", // 商品圖片
      "product_prices":1000,  // 商品價格
      "product_quantity":30, // 商品庫存
      "product_sold":0,  // 商品已售出└
      "product_rate":4, // 商品評價 (星)
      "status":1  // 狀態
    }
  ],
  "message":"ajax.success_getShopProduct_01"
}
```

### 取得買家上架商品  /api/getBuyerProduct

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "catalog_id":1, // 不帶參數為全部分類
  "page":1, // 不帶參數為第一頁
}
```

Reponse 

```javascript
{
  "status":1,
  "data":[
    {
      "id":1,  // product id
      "seller_id":1, // 賣場 id 
      "shop_catalog_parent_id": 1,// 通用分類父層id 
      "shop_catalog_id": 2, // 通用分類子層id 
      "shop_catalog_tree": ",1,2,", // 通用分類樹 
      "catalog_id":1,  // 分類id 
      "catalog_name":"A", // 分類名稱
      "product_name":"DELL27\u578bUltraSharp2KIPS\u7a84\u908a\u6846\u7f8e\u578b\u87a2\u5e55U2722D", // 商品名稱
      "product_sku":"skuA", // 商品編號
      "product_option":["G","H","I","J","K","L","M","N"], // 商品規格-選項
      "product_picture":"../assets/images/noimg.jpg", // 商品圖片
      "product_prices":1000,  // 商品價格
      "product_quantity":30, // 商品庫存
      "product_sold":0,  // 商品已售出
      "product_rate":4, // 商品評價 (星)
      "status":1  // 狀態
    }
  ],
  "message":"ajax.success_getShopProduct_01"
}
```

### 取得買家商品資訊 (單一)  /api/getBuyerProductInfo

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id":1
}
```

Reponse 

```javascript
{
  "status":1,
  "data":{
      "id":1,  // product id
      "seller_id":1, // 賣場 id 
      "shop_catalog_parent_id": 1,// 通用分類父層id 
      "shop_catalog_id": 2, // 通用分類子層id 
      "shop_catalog_tree": ",1,2,", // 通用分類樹 
      "catalog_id":1,  // 分類id 
      "catalog_name":"A", // 分類名稱
      "product_name":"DELL27\u578bUltraSharp2KIPS\u7a84\u908a\u6846\u7f8e\u578b\u87a2\u5e55U2722D", // 商品名稱
      "product_sku":"skuA", // 商品編號
      "product_option":["G","H","I","J","K","L","M","N"], // 商品規格-選項
      "product_prices":1000,  // 商品價格
      "product_quantity":30, // 商品庫存
      "product_sold":0,  // 商品已售出
      "product_rate":4, // 商品評價 (星)
      "status":1  // 狀態
  },
  "message":"ajax.success_getShopProductInfo_01"
}
```

### 新增買家上架商品  /api/createBuyerProduct

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "shop_catalog_parent_id":1,
  "shop_catalog_id":1, 
  "catalog_id":1, 
  "product_picture": "[
                        'data:image/jpeg;base64,abcdefg...',
                        'data:image/jpeg;base64,abcdefg...'
                      ]", 
  "product_name":"產品名稱", 
  "product_sku":"產品SKU", 
  "product_option":"規格1|規格2", 
  "product_description":"商品描述", 
  "product_prices":1000, 
  "product_quantity":100, 
  "status":1, 
}
```

Reponse 

```javascript
{
  "status":1,
  "data":[],
  "message":"ajax.success_createBuyerProduct_01"
}
```

### 編輯買家上架商品  /api/editBuyerProduct

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id":1,
  "shop_catalog_parent_id":1,
  "shop_catalog_id":1, 
  "catalog_id":1,  
  "product_name":"產品名稱", 
  "product_sku":"產品SKU", 
  "product_option":"規格1|規格2", 
  "product_description":"商品描述", 
  "product_prices":1000, 
  "product_quantity":100, 
  "status":1, 
}
```

Reponse 

```javascript
{
  "status":1,
  "data":[],
  "message":"ajax.success_editBuyerProduct_01"
}
```

### 刪除買家上架商品 （下架） /api/deleteBuyerProduct

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id":1
}
```

Reponse 

```javascript
{
  "status":1,
  "data":[],
  "message":"ajax.success_deleteBuyerProduct_01"
}
```

### 取得買家上架商品圖片列表 /api/getBuyerProductPicture

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id":1
}
```

Reponse 

```javascript
{
  "status":1,
  "data":[
        {
            "id": 3,
            "seller_id": 1,
            "product_id": 1,
            "catalog_id": null,
            "option": null,
            "order_by": 8,
            "picture": "https://img.shopping166.net/a4e662a470e16f6442d96e05853da9e4.jpg",
            "create_time": "2021-12-23 10:05:26",
            "status": 1
        }
  ],
  "message":"ajax.success_getBuyerProductPicture_01"
}
```

### 刪除買家上架商品圖片 /api/deleteBuyerProductPicture

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id":1 // 圖片ID
}
```

Reponse 

```javascript
{
  "status":1,
  "data":[],
  "message":"ajax.success_deleteBuyerProductPicture_01"
}
```

### 編輯買家上架商品圖片 /api/editBuyerProductPicture

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id":1, // 圖片ID
  "option" : "", // 指定規格, 未指定則為空
  "order_by" : 1 // 排序
}
```

Reponse 

```javascript
{
  "status":1,
  "data":[],
  "message":"ajax.success_editBuyerProductPicture_01"
}
```

### 新增買家上架商品圖片 /api/addBuyerProductPicture

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "product_id":1, // 產品ID
  "picture" : '["base64 IMG data","base64 IMG data",]' // base64圖片資料, json字串
}
```

Reponse 

```javascript
{
  "status":1,
  "data":[],
  "message":"ajax.success_addBuyerProductPicture_01"
}
```

### 取得買家賣場資訊 /api/getBuyerShopDetail

Request 

```javascript
{
  "seller_id" : "1"
}
```

Reponse 

```javascript
{
    "status": 1,
    "data": {
        "carousel_setting": 0,  // 輪播開關
        "event_setting": 1, // 活動開關
        "shop_logo": "NOIMG",
        "shop_name": "測試商店22",
        "email": "123@gmail.com",
        "product_count": 48,
        "product_rate": "4.9",
        "join": "4週前"
    },
    "message": "ajax.success_getBuyerShopDatail_01"
}
```

### 編輯買家賣場資訊 /api/editBuyerShopDetail

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "shop_name" : "賣場名稱",
  "shop_logo" : '["圖片base64字串"]',
  "carousel_setting": 0,  // 輪播開關
  "event_setting": 1, // 活動開關
}
```

Reponse 

```javascript
{
    "status": 1,
    "data": [],
    "message": "ajax.success_editBuyerShopDatail_01"
}
```

### 取得賣家中心的訂單列表  /api/getSellerOrder


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "status":"1", // 不帶預設為全部, 需登入
  // 0: 取消 , 1:待付 ,2:待出貨 , 3:待收件 , 4:已完成
  "page":1, // 不帶參數為第一頁
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[
  {
    "id":1, // 訂單ID
    "seller_id":1, // 賣場ID
    "buyer_id":1, // 買家ID
    "shipping": "蝦拼宅配", // 物流方式
    "shipping_order_id": "", // 物流單號
    "payment": "貨到付款", // 支付方式
    "prices":5000, // 訂單金額
    "data":[{   // 訂單商品 , 多個
      "id":3, // 購物車ID
      "seller_id":1, // 賣家ID
      "buyer_id":1, // 買家ID
      "product_id":1, // 商品ID 
      "product_name":"DELL27\u578bUltraSharp2KIPS\u7a84\u908a\u6846\u7f8e\u578b\u87a2\u5e55U2722D",
      "product_option":"A", // 商品規格
      "product_quantity":2, // 購買數量
      "product_prices":1000, // 小計金額
      "status":1
      }],
    "comment": {
      "id": 1,
      "seller_id": 1002,
      "product_id": 1,
      "order_id": 42,
      "name": "從束手無策資產重組", // 評論標題
      "email": "test@111.com",
      "rate": 4,// 評論評分
      "comment": "ㄗㄑㄨㄕㄘㄕㄕㄕㄕㄕ", // 評論內文
      "create_time": "2022-03-16 17:16:45",
      "status": 1
    },
    "create_time":"2021-09-30 04:39:38",
    "shop_id": 1, // 店鋪ID
    "shop_name": "測試商店", //店鋪名稱
    "shop_logo": "", // 店鋪LOGO
    "status":2 // 訂單狀態
  }],
  "message":"ajax.success_getSellerOrder_01"
}
```

### 訂單 - 出貨  /api/setSellerOrderShipping

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "order_id":"1", // 訂單ID
  "shipping_order_id": "1", // 物流單號
}
```

Reponse 

```javascript
{
    "status": 1,
    "data": [],
    "message": "ajax.success_setSellerOrderShipping_01"
}
```

### 訂單 - 退單  /api/setSellerOrderCancel
Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "order_id":"1", // 訂單ID
}
```

Reponse 

```javascript
{
    "status": 1,
    "data": [],
    "message": "ajax.success_setSellerOrderCancel_01"
}
```

### 取得退貨單詳情  /api/getSellerReturnOrder


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "page":1, // 不帶參數為第一頁
  "status":"1" // 不帶預設為全部
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[
        {
            "id": 1,  // 退貨單ID
            "seller_id": 1,
            "buyer_id": 1,
            "shop_order_id": 1, // 購物訂單
            "shipping_order": "55555",  // 買家退貨 物流單
            "items": "",
            "create_time": "2021-11-16 15:58:49", // 創建時間
            "shop_id": 1, // 店鋪ID
            "shop_name": "測試商店", //店鋪名稱
            "shop_logo": "", // 店鋪LOGO
            "status": 4,  // 退貨狀態
            "status_name": "已退貨",  // 狀態名稱
            "data": [   // 退貨商品資料
                {
                    "id": 20,
                    "seller_id": 1,
                    "buyer_id": 1,
                    "product_id": 1,
                    "product_name": "LOGIS｜雙色電腦椅 網布升級 透氣椅 全網椅 辦公椅 主管椅 台灣製 椅子 書桌椅 人體工學椅",
                    "product_option": "黑",
                    "product_quantity": 1,
                    "product_prices": 4000,
                    "shipping_fee": 0,
                    "status": 1,
                    "product_picture": "../../assets/images/noimg.jpg"
                }
            ]
        }
  ],
  "message":"ajax.success_getSellerReturnOrder_01"
}
```


### 退貨單 - 確認  /api/setReturnOrderConfirm


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "order_id":1, // 退貨單id
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[],
  "message":"ajax.success_setReturnOrderConfirm_01"
}
```


### 退貨單 - 取消  /api/setReturnOrderCancel


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "order_id":1, // 退貨單id
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[],
  "message":"ajax.success_setReturnOrderCancel_01"
}
```

### 退貨單 - 完成  /api/setReturnOrderSuccess


Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "order_id":1, // 退貨單id
}
```

Reponse 

```javascript
{
  "status":1, 
  "data":[],
  "message":"ajax.success_setReturnOrderSuccess_01"
}
```

### 取得賣家的所有商品評論  /api/getSellerComment

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "page":1, // 不帶參數為第一頁
}
```

Reponse 

```javascript
{
  "status":1,
  "data":[{
    "id":1, // 評價ID 
    "seller_id":1, // 賣場ID
    "product_id":1, // 商品ID
    "name":"\u6e2c\u8a661", // 評價標題
    "email":"1@2.3", // 評價者, email
    "rate":5, // 評價星數
    "comment":"1234567890",  // 評價內文
    "create_time":"2021-09-23 15:16:01", // 發表時間
    "status":1
  }],
  "message":"success message"
}
```

### 取得賣家物流設定  /api/getShippingSetting

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio"
}
```

Reponse 

```javascript
{
  "status":1,
  "data": [
                {
                    "id": 1,  // 支付ID
                    "name": "宅配", // 支付名稱
                    "status" : 1   // 開關, 0關 1開
                }, 
                {
                    "id": 2,
                    "name": "7-11", 
                    "status" : 1 
                },
                {
                    "id": 3,
                    "name": "黑貓", 
                    "status" : 0  
                }
  ],
  "message":"success message"
}
```


### 編輯賣家物流設定  /api/editShippingSetting

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "shipping_setting" : "[1,2,3]"
}
```

Reponse 

```javascript
{
  "status":1,
  "data": [],
  "message":"success message"
}
```

### 取得賣家金流設定  /api/getPaymentSetting

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio"
}
```

Reponse 

```javascript
{
  "status":1,
  "data": [
                {
                    "id": 1,  // 支付ID
                    "name": "貨到付款", // 支付名稱
                    "status" : 1   // 開關, 0關 1開
                }, 
                {
                    "id": 2,
                    "name": "信用卡", 
                    "status" : 1 
                },
                {
                    "id": 3,
                    "name": "LINEPAY", 
                    "status" : 0  
                }
  ],
  "message":"success message"
}
```


### 編輯賣家金流設定  /api/editPaymentSetting

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "payment_setting" : "[1,2,3]"
}
```

Reponse 

```javascript
{
  "status":1,
  "data": [],
  "message":"success message"
}
```


### 取得賣家標嵌  /api/getStoreTag

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio"
}
```

Reponse 

```javascript
{
  "status":1,
  "data": [
        {
            "id": 1,
            "catalog_name": "所有",
            "order_by": 7,
            "status": 0
        },
        {
            "id": 20,
            "catalog_name": "眼鏡",
            "order_by": 2,
            "status": 1
        }
  ],
  "message":"success message"
}
```

### 編輯賣家標嵌  /api/editStoreTag

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id" : 1, // tag id
  "catalog_name" : "1234567qweqefdjisaodujasio", // tag name
  "order_by" : 8, // order by
  "status" : 1 // status
}
```

Reponse 

```javascript
{
  "status":1,
  "data": [],
  "message":"success message"
}
```

### 新增賣家標嵌  /api/createStoreTag

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "catalog_name" : "1234567qweqefdjisaodujasio", // tag name
  "order_by" : 8, // order by
  "status" : 1 // status
}
```

Reponse 

```javascript
{
  "status":1,
  "data": [],
  "message":"success message"
}
```
### 刪除賣家標嵌  /api/deleteStoreTag

Request 

```javascript
{
  "email" : "email",
  "token" : "1234567qweqefdjisaodujasio",
  "id" : 1, // tag id
}
```

Reponse 

```javascript
{
  "status":1,
  "data": [],
  "message":"success message"
}
```

### 測試用  /api/test'












