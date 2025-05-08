import NavigatorBar from "@/Components/NavigatorBar";
import Sidebar from "@/Components/Sidebar";
import { Head, usePage, router } from "@inertiajs/react";
import { useEffect, useState } from "react";
import "../../css/layouts/ParentsLayout.css";

export default function ParentsLayout({ header, children }) {
    const { auth } = usePage().props;
    const url = window.location.pathname;
    const getInitialSidebarState = () => {
        if (url.includes('/reading/') || url.includes('/create_chapter/') || url.includes('/edit_chapter/')) {
            return false; 
        }
        return true;
    };

    const [sidebarOpen, setSidebarOpen] = useState(getInitialSidebarState());
    useEffect(() => {
        setSidebarOpen(getInitialSidebarState());
    }, [url]);

    const toggleSidebar = () => {
        setSidebarOpen(!sidebarOpen);
    };

    return (
        <div className="flex flex-row">
            <Head title="Monarch Project" />
            <Sidebar isOpen={sidebarOpen} toggleSidebar={toggleSidebar} />
            <div className={`flex-col flex container ${sidebarOpen ? "" : "sidebar-closed"}`}>
                <NavigatorBar 
                    auth={auth} 
                    isOpen={sidebarOpen} 
                    toggleSidebar={toggleSidebar}
                    removeFixed={true}
                 />
                <main className={`main-content flex flex-col ${sidebarOpen ? "" : "sidebar-closed"}`}>
                    {children}
                </main>
            </div>
        </div>
    );
}
