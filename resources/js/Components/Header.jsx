import { Link } from "@inertiajs/react";
import { FaArrowRight } from "react-icons/fa";
export default function Header({ title, url = "", is_expand = false }) {
    return (
        <div className="flex justify-between items-center text-2xl mb-4">
            <Link href = {url}><h2>{title}</h2></Link>
            {is_expand && <Link href="" style={{ paddingRight: "1rem" }}><FaArrowRight /></Link>}
        </div>
    );
}