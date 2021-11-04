import React, { useState, useEffect } from 'react';
import Header from '../../components/header';
import { Route } from 'react-router-dom';
import Articles from './articles';
import Courses from './courses';
import CourseDetails from './courseDetails';
import CheckResult from '../../components/cart/checkResult';
import BookDetails from './bookDetails';
import Books from './books';
import {
    Login,
    Register,
    ForgotPassword,
    CodeVerification,
    ResetPassword,
    CodeVerificationForgot,
} from '../../components/account';
import Home from './home';
import ITinTeaching from './ITinTeaching';
import OtherMaterials from './otherMaterials';
import ArticleDetails from './articleDetails';
import ExamIndex from './exam';
import CheckResultExam from '../../components/cart/checkResultExam';

const HomePages = (props) => {
    const { changeRender } = props;
    const [reRender, setReRender] = useState(false);// Dành cho login/ logout

    const [checkLoggedIn, setCheckLoggedIn] = useState(false);
    // const [reRender, setReRender] = useState(false);

    const handleCheckLoggedIn = () => {
        if (localStorage.getItem('access_token')) {
            let token = localStorage.getItem('access_token');
            if(token) {
                setCheckLoggedIn(true)
            }
            else {
                setCheckLoggedIn(false);
            }
        }
        else {
            setCheckLoggedIn(false);
        }
    };

    useEffect(() => {
        handleCheckLoggedIn();
    }, [reRender]);

    return (
        <>
            <Header setReRender={setReRender} checkLoggedIn={checkLoggedIn}/>
            <div>
                {/*  Route home page */}
                <Route path="/dang-ky" exact component={Register} />
                <Route
                    path="/dang-nhap"
                    exact
                    render={(props) => (
                        <Login
                            changeRender={changeRender}
                            setReRender={setReRender}
                            checkLoggedIn={checkLoggedIn}
                            {...props}
                        />
                    )}
                />
                <Route path="/quen-mat-khau" exact component={ForgotPassword} />
                <Route path="/xac-nhan-ma" exact component={CodeVerification} />
                <Route
                    path="/xac-nhan-ma-quen-mat-khau"
                    exact
                    component={CodeVerificationForgot}
                />
                <Route
                    path="/dat-lai-mat-khau"
                    exact
                    component={ResetPassword}
                />
                <Route path="/sach" exact component={Books} />
                <Route
                    path="/sach/:id"
                    exact
                    component={() => (
                        <BookDetails changeRender={changeRender} />
                    )}
                />
                <Route
                    path="/khoa-hoc/:id/"
                    exact
                    component={() => (
                        <CourseDetails changeRender={changeRender} />
                    )}
                />
                <Route path="/khoa-hoc" exact component={Courses} />
                <Route path="/tin-tuc" exact component={Articles} />
                <Route
                    path="/it-trong-day-hoc"
                    exact
                    component={ITinTeaching}
                />
                <Route
                    path="/check-result-payment"
                    exact
                    component={CheckResult}
                />
                <Route
                    path="/check-result-payment-exam"
                    exact
                    component={CheckResultExam}
                />
                <Route
                    path="/chi-tiet-bai-viet/:id"
                    exact
                    component={ArticleDetails}
                />
                <Route
                    path="/tai-lieu-khac/:id"
                    exact
                    component={OtherMaterials}
                />
                <Route
                    path="/tai-lieu-mien-phi"
                    exact
                    component={OtherMaterials}
                />
                <Route path="/thi-on-line" exact component={ExamIndex} />
                <Route
                    path="/"
                    exact
                    component={() => <Home changeRender={changeRender} />}
                />
            </div>
        </>
    );
};

export default HomePages;
