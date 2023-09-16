import React from "react";
import '../css/CommonSwiperArrow.css'
class CommonSwiperLeftArrow extends React.Component {
    render() {
        return(
            <div className="swiperArrow left">
                <span></span>
                <span></span>
                <span></span>
            </div>
        )
    }
}

class CommonSwiperRightArrow extends React.Component {
    render() {
        return(
            <div className="swiperArrow right">
                <span></span>
                <span></span>
                <span></span>
            </div>
        )
    }
}

export default CommonSwiperLeftArrow
export {CommonSwiperRightArrow}