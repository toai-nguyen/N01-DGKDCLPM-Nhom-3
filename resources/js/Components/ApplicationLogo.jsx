import image from '../../../public/image/download.png';

export default function ApplicationLogo(props) {
    return (
        <img src= {image} {...props} alt="logo" style={{display: "flex", flexGrow:"1", height:"2.5rem"}} />
    );
}
