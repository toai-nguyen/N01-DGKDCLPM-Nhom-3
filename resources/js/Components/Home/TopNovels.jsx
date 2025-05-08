import { MdNavigateNext, MdNavigateBefore } from "react-icons/md";
import React, { useRef, useState } from "react";
import { Link } from "@inertiajs/react";
import Header from "../Header";
import PopularNovelsCard from "../PopularNovelsCard";
import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import "../../../css/components/home/TopNovels.css";


export default function TopNovels({ novels }) {
    const swiperRef = useRef(null);
    const [isTransitioning, setIsTransitioning] = useState(false);

    const nextSlide = () => {
        if (isTransitioning || !swiperRef.current) return;
        setIsTransitioning(true);
        swiperRef.current.slideNext();
        setTimeout(() => {
            setIsTransitioning(false);
        }, 500);
    };

    const prevSlide = () => {
        if (isTransitioning || !swiperRef.current) return;
        setIsTransitioning(true);
        swiperRef.current.slidePrev();
        setTimeout(() => {
            setIsTransitioning(false);
        }, 500);
    };

    return (
        <div className="popular-container">
            <h2 className="popular-title">Popular Title</h2>
            <div className="popular-content">
                <Swiper
                    modules={[Navigation, Pagination, Autoplay]}
                    slidesPerView={1}
                    onSwiper={(swiper) => {
                        swiperRef.current = swiper;
                    }}
                    autoplay={{ delay: 10000, disableOnInteraction: false }}
                    loop={true}
                >
                    {novels.map((novel) => (
                        <SwiperSlide key={novel.id}>
                            <Link href = {route("view-novel", novel.id)} ><PopularNovelsCard novel={novel} /></Link>
                        </SwiperSlide>
                    ))}
                </Swiper>
                <div className="flex control-container">
                    <button
                        className="carousel-button prev"
                        onClick={prevSlide}
                        disabled={isTransitioning}
                    >
                        <MdNavigateBefore className="btn-prev" size={30} />
                    </button>
                    <button
                        className="carousel-button next"
                        onClick={nextSlide}
                        disabled={isTransitioning}
                    >
                        <MdNavigateNext className="btn-next" size={30} />
                    </button>
                </div>
            </div>
        </div>
    );
}