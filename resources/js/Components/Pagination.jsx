import { Link } from "@inertiajs/react";

export default function Pagination({ links }) {
    return (
        <div className="flex items-center justify-center space-x-1">
            {links.map((link, key) => {
                return (
                    <Link
                        key={key}
                        href={link.url || "#"}
                        className={`px-4 py-2 border rounded ${
                            link.url 
                                ? link.active
                                    ? 'bg-indigo-600 text-white'
                                    : 'bg-white hover:bg-gray-100'
                                : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                        }`}
                        dangerouslySetInnerHTML={{
                            __html: link.label,
                        }}
                    />
                );
            })}
        </div>
    );
}