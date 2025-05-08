import "../../css/components/home/TopNovels.css";

export default function PopularNovelsCard({ novel }) {
    return (
        <div className="popular-novel">
            <div className="popular-novel-image">
                <img src={novel.image_url} alt={novel.title} className="" />
            </div>
            <div className="popular-novel-info">
                <div>
                    <h3 className="">{novel.title}</h3>
                    <p className="description">{novel.description}</p>
                </div>
                <div className="sub-info-container d-flex flex-columnb">
                    <p className="author-name">{novel.author_name}</p>
                    <div className="sub-info-container flex">
                        <div className="tag-list">
                            {novel.tags.map((tag) => (
                                <span
                                    key={tag.id}
                                    className="bg-gray-400 tag-item"
                                >
                                    {tag.name}
                                </span>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
