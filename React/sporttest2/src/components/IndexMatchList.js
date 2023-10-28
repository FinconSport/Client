import React from "react";
import IndexMatchMenuTab from "./IndexMatchMenuTab"
import IndexMatchMenuPanel from "./IndexMatchMenuPanel"
import Cookies from 'js-cookie';
import { langText } from "../pages/LanguageContext";
import '../css/IndexMatchList.css';

const MatchListStyle = {
    width: '100%',
    height: 'calc(100% - 21.5rem)',
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
		document.getElementById('panelContiner').scrollTo({top: 0, behavior: 'smooth'});
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
        // 這個紀錄給game頁用
		Cookies.set('sport', sport_id, { path: '/' })
    }

    componentDidMount() {
        let res = this.props.api_res
        let rKey = null
        // default menu
        for (const key in res.data) {
            if (res.data.hasOwnProperty(key) && res.data[key].total !== 0) {
                rKey = key
                this.setState({
                    menu_id: mapping[key][1]
                })
                window.menu = mapping[key][1]
                break; 
            }
        }
        // default sport
        let itemData = res.data[rKey].items
        let keys = Object.keys(itemData);
        window.sport = keys.find(key => itemData[key].count > 0)
	}

    render() {
        const res = this.props.api_res
        console.log(res)
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
                        <div style={{ width: '76%', marginLeft: '4%', overflowY: 'auto' }} id="panelContiner">
                            {
                                langText.Common.order.map((v, k) => {
                                    let key = this.state.menu_id === 0 ? 'early' : 'living'
                                    if( res?.data?.[key]?.items?.[v]?.count > 0 ) {
                                        let e = res.data[key].items[v]
                                        return (
                                            <div key={k}>
                                                <div key={k} onClick={() => this.handlePanelClick(v)}>
                                                    <IndexMatchMenuPanel name={e.name} sport={v} count={e.count} />
                                                </div>
                                            </div>
                                        );
                                    }

                                })
                            }
                            {/* {Object.entries(res.data).map(([key, value]) => {
                                if (value.total !== 0 && this.state.menu_id === mapping[key][1]) {
                                    return (
                                        <div style={{ paddingBottom: '2rem' }} key={key}>
                                            {Object.entries(value.items).map(([key2, e]) => {
                                                if (e.count !== 0) {
                                                    return (
                                                        <div key={key2} onClick={() => this.handlePanelClick(key2)}>
                                                            <IndexMatchMenuPanel name={e.name} sport={key2} count={e.count} />
                                                        </div>
                                                    );
                                                }
                                                return null;
                                            })}
                                        </div>
                                    );
                                }
                                return null;
                            })} */}
                        </div>
                    </div>
                </>
            )
        }
   }
}

export default IndexMatchList;