import Header from "../Header";
import { Swiper, SwiperSlide } from "swiper/react";
import { Pagination , Navigation, Autoplay, Mousewheel} from "swiper/modules";
import NovelsSecondary from "../NovelsSecondary";
export default function RowNovels({ title, novels }) {
    return (
        <div className="header md:overflow-hidden">
            <Header title={title} is_expand={true} />
            <Swiper
                breakpoints={{
                    0: {
                        slidesPerView: 4.5,
                    },
                    640: {
                        slidesPerView: 5.5,
                    },
                    768: {
                        slidesPerView: 6.5,
                    },
                    1024: {
                        slidesPerView: 8.5,
                    },
                }}
                spaceBetween={10}
                pagination={{
                    dynamicBullets: true,
                }}
                mousewheel={true}
                passiveListeners={true}
                modules={[Pagination, Navigation, Autoplay, Mousewheel]}
                loop={true}
                className="w-full"
            >
                {novels.map((novel) => (
                    <SwiperSlide key={novel.id}>
                        <NovelsSecondary novel={novel} />
                    </SwiperSlide>
                ))}
            </Swiper>
        </div>
    );
}
