import React from "react";
import { Link } from "react-router-dom";
import { FaChevronLeft } from 'react-icons/fa';
import 'bootstrap/dist/css/bootstrap.min.css';
import  "../css/ResultPage.css";
import styled from '@emotion/styled';

const ResultMenuBar = {
    display: 'flex',
	padding: '0 0.5rem 0 0.5rem',
    height: '3rem',
    position: 'relative',
    zIndex: 0
}

const ResultSportBar = {
    width: '100%',
    background: 'rgb(65, 91, 90)',
    borderTopRightRadius: '35px',
    borderTopLeftRadius: '35px',
    overflow: 'hidden scroll',
    padding: '0.5rem',
    marginTop: '1rem',
    display: 'flex',
    overflowX: 'scroll',
    height: '5rem',
    position: 'relative',
    zIndex: 0
}

const ResultSportItem = styled.div`
	background: rgb(65, 91, 90);
    color: white;
    border-radius: 15px;
    box-shadow: rgba(0, 0, 0, 0.5) 0px 0px 5px 1px;
    width: 3rem;
    text-align: center;
    margin-right: 0.5rem;
    margin-left: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
    position: relative;
    padding-bottom: 0.2rem;
`

const backIcon = {
    background: 'rgb(65, 91, 90)',
    borderRadius: '50%',
    color: 'white',
    width: '2rem',
    height: '2rem',
    padding: '0.2rem',
    marginTop: '0.6rem',
    marginRight: '1rem'
}

const SportOn = {
    color: 'rgb(65, 91, 90)',
    background: 'white',
}

const ResultSportImg = {
    width: '1.8rem',
    height: '1.8rem',
    marginTop: '0.5rem'
}


const sportObj = {
    // 1: 'Soccer',
    1: '足球',
    // 2: 'basketball',
    2: '籃球',
    // 3: 'baseball'
    3: '棒球'
}

class ResultMenuNav extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            sport_id: this.props.sport,
        }
    }

    // sport_id change call back
    // handleSportChange = (sport_id) => {
    //     sport_id = parseInt(sport_id)
    //     this.props.callBack(sport_id)
    // };

    handleSportChange = (sport_id) => {
        sport_id = parseInt(sport_id)
        window.sport = sport_id
        this.setState({
            sport_id: sport_id
        })
        this.props.callBack(window.sport)
    };

    render() {
        return (
            <>
                <div style={ResultMenuBar}>
                    <Link to="/mobile">
                        <FaChevronLeft style={backIcon} />
                    </Link>
                </div>
                <div style={ResultSportBar}>
                    {/* <div style={ResultSportBarSlider}> */}
                        {
                            Object.entries(sportObj).map(([k, v],i) => {
                                return(
                                    <ResultSportItem key={k} style={ parseInt(this.props.sport_id) === parseInt(k) ? SportOn : null } onClick={() => this.handleSportChange(parseInt(k))}>
                                        <img style={ResultSportImg} alt={ v.name } src={ parseInt(this.props.sport_id) === parseInt(k) ? require('../image/ball/ball-' + k + '.png') : require('../image/ball/ball-' + k + '-white.png') } />
                                        <p className="mb-0" style={{ fontSize: '0.7rem' }}>{ v }</p>
                                    </ResultSportItem>
                                )
                            })
                        }
                    {/* </div> */}
                </div>
            </>
        ) 
    }
}

export default ResultMenuNav;