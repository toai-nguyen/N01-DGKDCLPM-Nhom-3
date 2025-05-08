import DOMPurify from 'dompurify';
export default function ChapterContent({content, className}) {
    const sanitizedContent = DOMPurify.sanitize(content);
    return (
        <div 
            className = {`chapter-content prose max-w-none font-montserrat
                ${className}`}
            dangerouslySetInnerHTML={{__html: sanitizedContent}}
        >
        </div>
            
    );
}
{/* <p className="font-montserrat"style={{whiteSpace: "pre-line", fontWeight: "500"}}></p> */}