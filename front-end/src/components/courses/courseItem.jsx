import React from 'react'
import { Link } from 'react-router-dom'

const CourseItem = (props) => {
    return (
        <div className="bg-gray-50 shadow-md mr-2 rounded-md overflow-hidden mb-10">
            <div>
                <img src="./assets/images/slider/city.jpg" className="w-full h-36 object-cover" alt="" />
                <span></span>
            </div>
            <div className="px-4 py-2">
                <div className="my-1">
                    <a href="#" className="hover:text-indigo-600 duration-300">
                        <h3 className="font-semibold line-2">Lorem ipsum dolor sit amet consectetur</h3>
                    </a>
                </div>
                <div className="mb-1 flex justify-between">
                    <div className="space-x-2">
                        <i class="fad fa-book-open text-indigo-300"></i>
                        <span className="text-15 text-gray-500">43 Video</span>
                    </div>
                    <div className="space-x-2">
                        <i className="fad fa-clock text-indigo-600"></i>
                        <span className="text-15 text-gray-500">3h52p</span>
                    </div>
                </div>
                <div>
                    <a href="#" className="flex items-center space-x-2">
                        <img src="./assets/images/slider/city.jpg" className="w-9 h-9 object-cover rounded-full shadow-md border border-white duration-300 hover:opacity-70" alt="" />
                        <h4 className="font-semibold duration-300 hover:text-green-900">Tên giáo viên</h4>
                    </a>
                </div>
            </div>
            <div className="border-t border-gray-300 p-4 flex justify-between">
                <div>
                    <label htmlFor="" className="text-indigo-600 font-semibold">390.000<sup>đ</sup></label>
                    <label htmlFor="" className="line-through text-xs ml-1">490.000<sup>đ</sup></label>
                </div>
                <Link href="#" className="space-x-2 group">
                    <span className="font-semibold group-hover:text-indigo-600">Chi tiết</span>
                    <i className="far fa-arrow-right transform duration-300 group-hover:translate-x-1.5 group-hover:text-indigo-600"></i>
                </Link>
            </div>
        </div>
    )
}
export default CourseItem