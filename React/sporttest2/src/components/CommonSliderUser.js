import React from "react";
import CommonLogs from "./CommonLogs";
import CommonRules from "./CommonRules";
import CommonNotice from "./CommonNotice";
import styled from "@emotion/styled";
import { langText } from "../pages/LanguageContext";
import { Link } from "react-router-dom";
import { MdAutorenew } from "react-icons/md";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faXmark } from "@fortawesome/free-solid-svg-icons";
import "../css/CommonSliderUser.css";

const xStyle = {
  padding: "10px",
  fontSize: "25px",
  fontWeight: "700",
  color: "white",
  position: "absolute",
  top: 0,
  left: 0,
  zIndex: 3,
};

const userStyle = {
  backgroundColor: "#3c5454",
  textAlign: "center",
  color: "white",
  height: "15rem",
};

const menuStyle = {
  height: "calc(100% - 15rem)",
  padding: "1rem 2rem",
};

const userImg = {
  width: "8rem",
  marginTop: "1rem",
  marginBottom: "0.5rem",
};

const balance = {
  fontWeight: "bold",
  alignItems: "center",
  color: "#c79e42 ",
};

const MenuBtn = styled.div`
  height: 2rem;
  line-height: 1rem;
  padding: 0.5rem;
  text-align: center;
  font-weight: 600;
  background: white;
  border-radius: 25px;
  margin-top: 1rem;
`;

class CommonSliderUser extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      isMenuOpen: false,
      isLogsOpen: false,
    };
  }

  // 刷新餘額
  refreshWallet = () => {
    this.props.callBack();
  };

  // 打開帳務頁
  openLogs = () => {
    // 關閉資訊頁
    this.handleMenuChange();
    this.setState({
      isLogsOpen: true,
    });
  };

  // 關閉帳務頁
  closeLogs = () => {
    this.setState({
      isLogsOpen: false,
    });
  };

  openNotice = () => {
    // 關閉資訊頁
    this.handleMenuChange();
    this.setState({
      isNoticeOpen: true,
    });
  };
  closeNotice = () => {
    this.setState({
      isNoticeOpen: false,
    });
  };

  // 打開帳務頁
  openGameRule = () => {
    // 關閉資訊頁
    this.handleMenuChange();
    this.setState({
      isGameRuleOpen: true,
    });
  };

  // 關閉帳務頁
  closeGameRule = () => {
    this.setState({
      isGameRuleOpen: false,
    });
  };

  // 打開或關閉
  handleMenuChange = () => {
    this.setState({
      isMenuOpen: !this.state.isMenuOpen,
    });
    if (this.state.isMenuOpen) {
      this.setState({ isMenuOpen: false, class: "on" });
    } else {
      this.setState({ isMenuOpen: true, class: "mask" });
    }
  };

  render() {
    const res = this.props.api_res;
    if (res !== undefined) {
      return (
        <div>
          <div
            className={this.state.class}
            onClick={this.handleMenuChange}
          ></div>
          <div
            className="memberInfo"
            style={{ right: this.state.isMenuOpen === false ? "-300px" : "0" }}
          >
            <div
              style={{
                height: "100vh",
                height: "calc(var(--vh, 1vh) * 100)",
                backgroundColor: "#415b5a",
              }}
            >
              <div style={userStyle}>
                <img
                  alt="user"
                  style={userImg}
                  src={require("../image/user.png")}
                />
                <div style={{ marginBottom: "0.5rem" }}>{res.data.account}</div>
                <div
                  className="balance"
                  style={{ ...balance, paddingBottom: "0.5rem" }}
                  onClick={this.refreshWallet}
                >
                  <span>{res.data.balance}</span>
                  <span
                    style={{
                      marginLeft: "0.5rem",
                      fontSize: "1.1rem",
                      color: "white",
                    }}
                  >
                    <MdAutorenew
                      className={
                        this.props.isRefrehingBalance === true
                          ? "rotateRefresh"
                          : ""
                      }
                    />
                  </span>
                </div>
              </div>
              <div style={menuStyle}>
                <MenuBtn onClick={this.openLogs}>
                  {langText.CommonLogs.record}
                </MenuBtn>
                <MenuBtn onClick={this.openGameRule}>
                  {langText.CommonRulesTitles.gameRules}
                </MenuBtn>
                <MenuBtn onClick={this.openNotice}>
                  {langText.CommonNotice.notice}
                </MenuBtn>
                <MenuBtn>
                  <Link style={{color: '#415b5a'}} to="/mobile/result">
                      {langText.ResultTitle.result}
                    </Link>
                </MenuBtn>
              </div>
            </div>
          </div>
          {this.state.isMenuOpen && (
            <FontAwesomeIcon
              style={xStyle}
              onClick={this.handleMenuChange}
              icon={faXmark}
            />
          )}
          <CommonLogs
            isLogsOpen={this.state.isLogsOpen}
            callBack={this.closeLogs}
          />
          <CommonRules
            isGameRuleOpen={this.state.isGameRuleOpen}
            callBack={this.closeGameRule}
          />
          <CommonNotice
            isNoticeOpen={this.state.isNoticeOpen}
            callBack={this.closeNotice}
          />
        </div>
      );
    }
  }
}

export default CommonSliderUser;
