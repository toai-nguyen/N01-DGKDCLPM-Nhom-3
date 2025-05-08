export default function UserAvatar({src, alt, className}) {
    const $avatarUrl = "https://res.cloudinary.com/dvomghpsu/image/upload/v1741310377/novel_project/user_avartar/default_avatar.png";
    return (
        <img
            src={src ? src : $avatarUrl}
            alt={alt}
            className={`rounded-full ${className}`}
        />
    );
}  