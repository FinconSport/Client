import ReactDOM from "react-dom/client";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Layout from "./pages/Layout";
import Home from "./pages/Home";
import Match from "./pages/Match";
import Result from "./pages/Result";
import Game from "./pages/Game";
import MOrder from "./pages/MOrder";
import Maintain from "./pages/Maintain";
import E404 from "./pages/E404";
import E500 from "./pages/E500";
import IPError from "./pages/IPError";



export default function App() {
  return (
    <>
      <BrowserRouter>
        <Routes>
          <Route path="/mobile/" element={<Layout />}>
            <Route index element={<Home />} />
            <Route path="/mobile/index" element={<Home />} />
            <Route path="/mobile/game" element={<Game />} />
            <Route path="/mobile/match" element={<Match />} />
            <Route path="/mobile/result" element={<Result />} />
            <Route path="/mobile/m_order" element={<MOrder />} />
            <Route path="/mobile/m_maintain" element={<Maintain />} />
            <Route path="/mobile/m_404" element={<E404 />} />
            <Route path="/mobile/m_500" element={<E500 />} />
            <Route path="/mobile/m_ip" element={<IPError />} />
            <Route path="*" element={<E404 />} />
          </Route>
        </Routes>
      </BrowserRouter>
      
     
    </>
  );
}

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(<App />);