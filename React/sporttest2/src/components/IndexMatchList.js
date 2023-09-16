import React from "react";
import IndexMatchMenuTab from "./IndexMatchMenuTab"
import IndexMatchMenuPanel from "./IndexMatchMenuPanel"
import Cookies from 'js-cookie';
import { langText } from "../pages/LanguageContext";
import '../css/IndexMatchList.css';

const MatchListStyle = {
    width: '100%',
    display: 'flex',
    padding: '0 0.5rem'
}

const mapping = {
    'living': [langText.IndexMatchList.living, 1],
    'early': [langText.IndexMatchList.early, 0],
}

class IndexMatchList extends React.Component {

    constructor(props) {
		super(props);
		this.state = {
            menu_id: 0,
			sport_id: 1
		};
	}

    // 早盤 滾球 
    handleTabClick = (menu_id) => {
        menu_id = parseInt(menu_id)
        window.menu = menu_id
        this.setState({
            menu_id: menu_id
        })
    }

    // 足球 籃球 報球 ...
    handlePanelClick = (sport_id) => {
        sport_id = parseInt(sport_id)
        window.sport = sport_id
        this.setState({
            sport_id: sport_id
        })

        // 這個紀錄給game頁用
		Cookies.set('sport', sport_id, { path: '/' })
    }

    componentDidMount() {
        // if(this.props.api_res !== undefined) {
        //     const keys = Object.keys(this.props.api_res.data);
        //     const firstNonZeroTotalKey = keys.find(key => this.props.api_res.data[key].total !== 0);
        //     const items = this.props.api_res.data[firstNonZeroTotalKey].items;
        //     const firstNonZeroCountIndex =  Object.keys(items).find(key => items[key].count !== 0);
            
        //     window.menu = mapping[firstNonZeroTotalKey][1]
        //     window.sport = firstNonZeroCountIndex
        //     if(this.state.menu_id !== window.menu || this.state.sport_id !== window.sport) {
        //         this.setState({
        //             menu_id: window.menu,
        //             sport_id: window.sport,
        //         })
        //     }
        // }
	}

    render() {
        const res = this.props.api_res
        if( res !== undefined){
            return(
                <>
                    <div style={MatchListStyle}>
                        <div style={{width: '20%'}}>
                            {
                                Object.entries(res.data).map(([key, value]) => {
                                    return (
                                        value.total !== 0 &&
                                        <div key={key} onClick={()=>this.handleTabClick(mapping[key][1])}>
                                            {
                                                this.state.menu_id === mapping[key][1] ?
                                                <IndexMatchMenuTab text={mapping[key][0]} total={value['total']} key={key} selected />
                                                :
                                                <IndexMatchMenuTab text={mapping[key][0]} total={value['total']} key={key} selected={false} />
                                            }
                                        </div>
                                    )
                                })
                            }
                        </div>
                        <div style={{width: '76%', marginLeft: '4%'}}>
                            {
                                Object.entries(res.data).map(([key, value]) => {
                                    if (value.total !== 0) {
                                        return(
                                            <div key={key}>
                                                {
                                                    this.state.menu_id === mapping[key][1] ?
                                                    Object.entries(value.items).map(([key2, e]) => {
                                                        if(e.count !== 0) {
                                                            return(
                                                                <div key={key2} onClick={()=>this.handlePanelClick(key2)}>
                                                                    <IndexMatchMenuPanel name={e.name} sport={key2} count={e.count} menu_id={this.state.menu_id} sport_id={this.state.sport_id} />
                                                                </div>
                                                            )
                                                        }
                                                    })
                                                    :
                                                    null
                                                }
                                            </div>
                                        )
                                    }
                                })
                            }
                        </div>
                    </div>
                </>
            )
        }
   }
}

export default IndexMatchList;