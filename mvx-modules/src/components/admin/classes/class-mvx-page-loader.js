/* global appLocalizer */
import React, { Component } from 'react';
import PuffLoader from "react-spinners/PuffLoader";
class PageLoader extends Component {
    render() {
        return (
                <PuffLoader className="mvx-pre-loadder" color={"#cd0000"} size={200} loading={true} />
        );
    }
}
export default PageLoader;