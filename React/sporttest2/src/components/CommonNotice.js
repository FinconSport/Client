import React, { Component } from "react";
import { langText } from "../pages/LanguageContext"; //lang
import GetIni from "./AjaxFunction"; //ajax
import pako from "pako"; //api
import { AiFillCloseCircle } from "react-icons/ai"; //icon
import { TbArrowBigUpFilled } from "react-icons/tb"; //icon
import "../css/CommonNotice.css";

const ToTopStyle = {
  right: "0.5rem",
  bottom: "7rem",
  zIndex: 1,
  position: "absolute",
  background: "#c79e42",
  color: "white",
  borderRadius: "50%",
  fontSize: "2.5rem",
  padding: "0.3rem",
  opacity: 0.7
};

const NoticeWrapper = {
  fontWeight: 600,
  backgroundColor: "rgb(228 240 239 / 90%)",
  position: "fixed",
  width: "100%",
  height: "100%",
  zIndex: 2,
  transition: "all .5s ease 0s",
  MozTransition: "all .5s ease 0s",
  WebkitTransition: "all .5s ease 0s",
  OTransition: "all .5s ease 0s",
  WebkitOverflowScrolling: "touch",
  bottom: "calc(-100%)",
};

const NoticeWrapperOn = {
  bottom: "0",
};

const NoticeBetWrapper = {
  width: "100%",
  height: "92%",
  bottom: 0,
  backgroundColor: "rgb(65, 91, 90)",
  borderTopRightRadius: "35px",
  borderTopLeftRadius: "35px",
  position: "absolute",
  padding: "1rem 1rem 0 1rem",
};
const PageContainer = {
  overflowY: "auto",
  overflowX: "hidden",
  borderRadius: "15px",
  width: "100%",
  height: "90%",
  fontSize: "0.9rem",
  paddingBottom: "10px",
};

const NoticePageTitle = {
  position: "absolute",
  left: "1rem",
  top: "1rem",
  fontSize: "1.2rem",
};

const NoticePageClose = {
  position: "absolute",
  right: "1rem",
  top: "1rem",
  fontSize: "2rem",
};

const TabMenuWrapperCon = {
  padding: "0.5rem",
  gridColumnGap: " 0.5rem",
  display: "flex",
  overflowX: "scroll",
};

const TabMenuBtn = {
  background: "#445a5a",
  color: "#c4d3d3",
  height: "auto",
  lineHeight: "1rem",
  padding: "0.7rem",
  textAlign: "center",
  fontWeight: "600",
  fontSize: "1rem",
  borderRadius: "15px",
  boxShadow: "#00000080 0 0 9px 0px",
  border: "none",
  minWidth: "100px",
};

const TabMenuBtnActive = {
  background: "#445a5a",
  color: "#c19e4f",
  height: "auto",
  lineHeight: "1rem",
  padding: "0.7rem",
  textAlign: "center",
  fontWeight: "600",
  fontSize: "1rem",
  borderRadius: "15px",
  boxShadow: "#00000080 0 0 9px 0px",
  border: "none",
  minWidth: "100px",
};

class CommonNotice extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      page: 1,
      searchStatus: 0,
      fetchMoreLock: 0,
      activeTab: 99,
    };
  }

  // 關閉頁面
  closeNotice = () => {
    this.props.callBack();
  };

  handleTabChange = (sportCode) => {
    this.scrollToTop();
    this.setState({
      activeTab: sportCode,
    });
  };

  async caller(apiUrl) {
    const json = await GetIni(apiUrl);
    // uncompressed
    if (json.gzip) {
      const str = json.data;
      const bytes = atob(str)
        .split("")
        .map((char) => char.charCodeAt(0));
      const buffer = new Uint8Array(bytes).buffer;
      const uncompressed = JSON.parse(pako.inflate(buffer, { to: "string" }));
      json.data = uncompressed;
    }
    this.setState({
      notice_list: json.data,
    });
  }
  // 初始資料
  componentDidMount() {
    this.caller(
      "https://sportc.asgame.net/api/v2/index_notice?token=" +
        window.token +
        "&player=" +
        window.player
    );
  }

  // 滑到最上面
  scrollToTop = () => {
    document
      .getElementById("NoticeMain")
      .scrollTo({ top: 0, behavior: "smooth" });
  };

  render() {
    const { activeTab, notice_list } = this.state;
    const notice_cat = {
      0: langText.CommonNotice.system,
      1: langText.CommonNotice.soccer,
      2: langText.CommonNotice.basketball,
      3: langText.CommonNotice.baseball,
      4: langText.CommonNotice.iceball,
      5: langText.CommonNotice.tennis,
      6: langText.CommonNotice.football,
      7: langText.CommonNotice.snooker,
      8: langText.CommonNotice.tabletennis,
      9: langText.CommonNotice.volleyball,
    };

    if (notice_list) {
      const noticeArray = Object.values(notice_list);
      const allNotice = noticeArray.flat();



      return (
        <div
          style={{
            ...NoticeWrapper,
            ...(this.props.isNoticeOpen === true && NoticeWrapperOn),
          }}
        >
          <div style={NoticePageTitle}>{langText.CommonNotice.notice}</div>
          <AiFillCloseCircle
            style={NoticePageClose}
            onClick={this.closeNotice}
          />
          <div style={NoticeBetWrapper}>
            <div style={TabMenuWrapperCon}>
              {/* all btn */}
              <button
                onClick={() => this.handleTabChange(99)}
                style={activeTab == 99 ? TabMenuBtnActive : TabMenuBtn}
              >
                {langText.CommonNotice.notice}
              </button>
              {/* each btn */}
              {Object.entries(notice_cat).map(([key, val]) => (
                <button key={key}
                  onClick={() => this.handleTabChange(key)}
                  style={activeTab == key ? TabMenuBtnActive : TabMenuBtn}
                >
                  {val}
                </button>
              ))}
            </div>

            <div id="NoticeMain" style={PageContainer}>
              <div id="NoticeMainWrap" className="notice-tab">
                {/* all */}
                <div id="TabMainWrapperCon">
                  <div
                    className="content"
                    style={{ display: activeTab === 99 ? "block" : "none" }}
                  >
                    {allNotice
                      .sort((a, b) => {
                        const dateA = new Date(a.create_time);
                        const dateB = new Date(b.create_time);
                        return dateB - dateA; // 从新到旧排序
                      })
                      .map((v, l) => (
                        <div className="card" key={l}>
                          <div className="card-header">
                            <p className="notice-title">{v.title}</p>
                          </div>
                          <div className="card-body">
                            <p className="notice-context">{v.context}</p>
                            <p className="notice-date">{v.create_time}</p>
                          </div>
                        </div>
                      ))}
                  </div>
                  {/* all */}

                  {/* each from notice_list */}
                  {Object.entries(notice_cat).map(([key, val]) => (
                    <div  key={key}
                      className="content"
                      style={{ display: activeTab === key ? "block" : "none" }}
                    >
                      {notice_list[key] ? (
                        notice_list[key]
                          .sort((a, b) => {
                            const dateA = new Date(a.create_time);
                            const dateB = new Date(b.create_time);
                            return dateB - dateA; // 从新到旧排序
                          })
                          .map((v, l) => (
                            <div className="card" key={l}>
                              <div className="card-header">
                                <p className="notice-title">{v.title}</p>
                              </div>
                              <div className="card-body">
                                <p className="notice-context">{v.context}</p>
                                <p className="notice-date">{v.create_time}</p>
                              </div>
                            </div>
                          ))
                      ) : (
                        <div className="noRecord">
                          <p>{langText.CommonNotice.norecord}</p>
                        </div>
                      )}
                    </div>
                  ))}
                  {/* each from notice_list */}
                </div>
              </div>
            </div>
            <TbArrowBigUpFilled onClick={this.scrollToTop} style={ToTopStyle} />
          </div>
        </div>
      );
    }
  }
}

export default CommonNotice;
