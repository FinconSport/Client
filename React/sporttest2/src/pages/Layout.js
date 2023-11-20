import React, { useEffect, useState } from "react";
import { langText } from "../pages/LanguageContext";
import { Outlet } from "react-router-dom";
import "../css/Common.css";
import promptIcon from '../image/horizonPropmt.png'

const horizonPrompt = {
	background: '#666666',
	color: 'white',
	width: '100%',
	height: '100%',
	backgroundImage: `url(${promptIcon})`,
	backgroundRepeat: 'no-repeat',
	backgroundPosition: '50% 40%',
	textAlign: 'center'
}

const Layout = () => {
	const [orientation, setOrientation] = useState(
		window.innerWidth > window.innerHeight ? "h" : "v"
	);
	const handleResize = () => {
		setOrientation(
		window.innerWidth > window.innerHeight ? "h" : "v"
		);
	};

	useEffect(() => {
		window.addEventListener("resize", handleResize);
		return () => {
		window.removeEventListener("resize", handleResize);
		};
	}, []); // Empty dependency array ensures that the effect runs only once on mount

	return (
		orientation === 'h' ? 
		<div style={horizonPrompt}>
			<p className="fw-600" style={{ paddingTop: '27%' }}>
				{langText.Layout.horizonPrompt}
			</p>
		</div>
		:
		<Outlet />
	);
};

export default Layout;
