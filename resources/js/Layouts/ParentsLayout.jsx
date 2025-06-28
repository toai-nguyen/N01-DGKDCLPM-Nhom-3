import NavigatorBar from "@/Components/NavigatorBar";
import Sidebar from "@/Components/Sidebar";
import { Head, usePage, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import "../../css/layouts/ParentsLayout.css";

export default function ParentsLayout({ header, children }) {
    const { auth } = usePage().props;
    const url = window.location.pathname;
    
    const getInitialSidebarState = () => {
        // Tự động đóng sidebar trên màn hình nhỏ
        if (window.innerWidth <= 768) {
            return false;
        }
        
        if (url.includes('/reading/') || url.includes('/create_chapter/') || url.includes('/edit_chapter/')) {
            return false; 
        }
        return true;
    };

    const [sidebarOpen, setSidebarOpen] = useState(getInitialSidebarState());
    
    useEffect(() => {
        setSidebarOpen(getInitialSidebarState());
    }, [url]);

    // Thêm effect để xử lý responsive
    useEffect(() => {
        const handleResize = () => {
            if (window.innerWidth <= 768) {
                setSidebarOpen(false);
            }
        };

        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    const toggleSidebar = () => {
        setSidebarOpen(!sidebarOpen);
    };

    return (
        <div className="flex flex-row min-h-screen w-full overflow-x-hidden">
            <Head title="Monarch Project" />
            <Sidebar isOpen={sidebarOpen} toggleSidebar={toggleSidebar} />
            <div className={`flex flex-col container ${!sidebarOpen ? "sidebar-closed" : ""}`}>
                <NavigatorBar 
                    auth={auth} 
                    isOpen={sidebarOpen} 
                    toggleSidebar={toggleSidebar}
                    removeFixed={true}
                 />
                <main className={`main-content flex flex-col flex-1 ${!sidebarOpen ? "sidebar-closed" : ""}`}>
                    {children}
                </main>
            </div>
        </div>
    );
}
