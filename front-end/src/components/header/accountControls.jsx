import React from 'react'
import { Link } from 'react-router-dom'

const AccountControls = (props) => {
    return (
        <div className="flex items-center space-x-3">
            <div className="text-white space-x-3">
                <button className="search-open leading-5 left-1 px-3 p-2 rounded-sm bg-indigo-600 hover:bg-indigo-700 duration-300">
                    <i className="far fa-search font-medium"/>
                </button>
                <button type="button" className="relative cart bg-green-700 hover:bg-green-800 pr-3 leading-5 p-2 duration-500">
                    <i class="far fa-cart-plus font-medium"></i>
                    <label className="absolute top-0 right-1 font-medium text-15">4</label>
                </button>
            </div>
            <div>
                <Link to="/dang-nhap">
                    <a  className="relative hidden md:block btn-login duration-300 bg-transparent rounded-sm bg-green-700 hover:shadow-2xl hover:bg-green-800 hover:text-white px-4 py-2 text-white font-semibold">
                        <svg className="absolute top-0 left-0 w-full h-full fill-transparent">
                            <rect className="absolute btn-stroke top-0 left-0 w-full h-full fill-transparent stroke-2 stroke-dasharray animate-btnlogin"></rect>
                        </svg>
                        Đăng nhập
                    </a>
                </Link>
            </div>

            {/* <div className="hidden md:block">
                <Link to="/dang-ky" className="relative btn-register font-semibold duration-200 box-border bg-indigo-600 px-4 py-2 text-white hover:shadow-xl hover:bg-indigo-700 rounded-sm">
                    <span>Đăng ký</span>
                </Link>
            </div> */}

            <div className="nav-open w-6 text-2xl flex lg:hidden items-center text-white cursor-pointer hover:opacity-70">
                <i class="far fa-bars"></i>
            </div>
        </div>
    )
}
export default AccountControls