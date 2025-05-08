export default function ChapterHeader({number, title, updatedAt, className}) {
    return (
        <div
            className = {`p-4 ${className}`}
        >
            <h3>Chapter {number}: {title}</h3>
            <p className="font-montserrat"
                style={{fontSize: "0.8rem"}}    
            >Last updated: {updatedAt}</p>
        </div>
    );
}