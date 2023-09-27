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



class ResultMenuNav extends React.Component {
    constructor(props){
        super(props);
        this.state = {
            api_res: this.props.api_res
        }
    }
    componentDidMount() {
        let res = this.state.api_res
        if(window.sport === null) {
            window.sport = res.data[0].sport_id
        }
        this.props.callBack(window.sport)
	}

    handleSportChange = (sport_id) => {
        window.sport = sport_id
        this.props.callBack(window.sport)
    };

    render() {
        const res = this.state.api_res;
        if( res && window.sport !== null ) {
            return (
                <>
                    <div style={ResultMenuBar}>
                        <Link to="/mobile">
                            <FaChevronLeft style={backIcon} />
                        </Link>
                    </div>
                    <div style={ResultSportBar}>
                        {
                            res.data.map( v => {
                                return(
                                    <ResultSportItem key={v.sport_id} style={ parseInt(window.sport) === parseInt(v.sport_id) ? SportOn : null } onClick={() => this.handleSportChange(parseInt(v.sport_id))}>
                                        <img style={ResultSportImg} alt={ v.name } src={ parseInt(window.sport) === parseInt(v.sport_id) ? require('../image/ball/ball-' + v.sport_id + '.png') : require('../image/ball/ball-' + v.sport_id + '-white.png') } />
                                        <p className="mb-0" style={{ fontSize: '0.7rem' }}>{ v.name }</p>
                                    </ResultSportItem>
                                )
                            })
                        }
                    </div>
                </>
            ) 
        }
       
    }
}

export default ResultMenuNav;