import ParentsLayout from "@/Layouts/ParentsLayout";
import Header from "@/Components/Header";
import { Link } from "@inertiajs/react";
import axios from "axios";
import { useState, useEffect } from "react";
import Pagination from "@/Components/Pagination";

export default function Notifications({ notifications }) {
    const [notificationsList, setNotificationsList] = useState(notifications.data || []);
    const [loading, setLoading] = useState(false);
    // Mark notification as read when clicked
    const reloadNotifications = () => {
        setLoading(true);
        axios.get(route('notifications.get'))
            .then(response => {
                setNotificationsList(response.data.notifications.data || []);
                setLoading(false);
            })
            .catch(error => {
                console.error("Error reloading notifications:", error);
                setLoading(false);
            });
    };
    const markAsRead = (id) => {
        axios.post(route('notifications.mark-as-read'), { id })
            .then(() => {
                setNotificationsList(notificationsList.map(item => 
                    item.id === id ? {...item, read_at: new Date().toISOString()} : item
                ));
            })
            .catch(error => console.error("Error marking notification as read:", error));
    };
    
    // Mark all as read
    const markAllAsRead = () => {
        setLoading(true);
        axios.post(route('notifications.mark-as-read'))
            .then(() => {
                // Reload notifications from server after marking all as read
                reloadNotifications();
            })
            .catch(error => {
                console.error("Error marking all notifications as read:", error);
                setLoading(false);
            });
    };

    return (
        <ParentsLayout>
            <Header title="Notifications" />

            <div className="">
                
                {notificationsList.length > 0 ? (
                    <div className="mt-5">
                        <div className="flex justify-end mb-4">
                            <button 
                                onClick={markAllAsRead}
                                className="text-blue-600 hover:text-blue-800"
                            >
                                Mark all as read
                            </button>
                        </div>
                        
                        <div className="bg-white shadow overflow-hidden sm:rounded-md">
                            <ul className="divide-y divide-gray-200">
                                {notificationsList.map(notification => (
                                    <li key={notification.id} 
                                        className={`${!notification.read_at ? 'bg-blue-50' : ''}`}
                                    >
                                        <Link 
                                            href={route('chapter.show', {
                                                novel_id: notification.data.novel_id,
                                                chapter_id: notification.data.chapter_id
                                            })}
                                            onClick={() => markAsRead(notification.id)}
                                            className="block hover:bg-gray-50"
                                        >
                                            <div className="px-4 py-4 sm:px-6">
                                                <div className="flex items-center justify-between">
                                                    <p className="text-sm font-medium text-indigo-600 truncate">
                                                        {notification.data.novel_title}
                                                    </p>
                                                    <div className="ml-2 flex-shrink-0 flex">
                                                        <p className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            New chapter
                                                        </p>
                                                    </div>
                                                </div>
                                                <div className="mt-2 flex justify-between">
                                                    <div className="sm:flex">
                                                        <p className="text-sm text-gray-700">
                                                            {notification.data.author_name} posted Chapter {notification.data.chapter_number}: {notification.data.chapter_title}
                                                        </p>
                                                    </div>
                                                    <p className="text-sm text-gray-500">
                                                        {notification.created_at}
                                                    </p>
                                                </div>
                                            </div>
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                        
                        {notifications.links && (
                            <div className="mt-5">
                                <Pagination links={notifications.links} />
                            </div>
                        )}
                    </div>
                ) : (
                    <div className="text-center py-10">
                        <svg className="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <h3 className="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                        <p className="mt-1 text-sm text-gray-500">You don't have any notifications yet.</p>
                    </div>
                )}
            </div>
        </ParentsLayout>
    );
}