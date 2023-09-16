import React from "react"
const GlobalData = 'peco is handsome'

const ReactContext = React.createContext(GlobalData)//* 创建一个初始化数据，可以为Array，Object

export default ReactContext;