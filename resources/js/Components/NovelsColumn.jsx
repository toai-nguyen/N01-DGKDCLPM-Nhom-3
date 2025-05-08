import NovelsCard from "./NovelsCard";
export default function NovelsColumn({ novels }) {
    return (
        <div className="bg-accent">
            <div className="grid gap-4 p-4">
                {novels.map(novel => (
                    <NovelsCard key={novel.id} novel={novel} />
                ))}
            </div>
        </div>
    );
}