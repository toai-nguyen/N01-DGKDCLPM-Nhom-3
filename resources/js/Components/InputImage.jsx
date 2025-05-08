import React, { useState, useCallback, useEffect } from 'react';
import { useDropzone } from 'react-dropzone';

const InputImage = ({ onChange, id, name, className = '', resetPreview, defaultImage }) => {
  const [previewImage, setPreviewImage] = useState(defaultImage || null);

  // Kích thước khung
  const FRAME_WIDTH = 200;
  const FRAME_HEIGHT = 300;

  // Cập nhật preview khi defaultImage thay đổi
  useEffect(() => {
    if (defaultImage) {
      setPreviewImage(defaultImage);
    }
  }, [defaultImage]);

  // Reset preview nếu cần
  useEffect(() => {
    if (resetPreview) {
      setPreviewImage(null);
    }
  }, [resetPreview]);

  // Xử lý khi file được drop hoặc chọn
  const onDrop = useCallback((acceptedFiles) => {
    const file = acceptedFiles[0];
    if (file && file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (event) => {
        setPreviewImage(event.target.result); // Lưu URL của ảnh gốc
      };
      reader.readAsDataURL(file);
      
      // Ensure we pass the actual file object to the parent component
      if (onChange) {
        onChange(file);
      }
    }
  }, [onChange]);

  const { getRootProps, getInputProps, isDragActive } = useDropzone({
    onDrop,
    accept: {
      'image/*': [] // Chỉ chấp nhận file hình ảnh
    },
    multiple: false, // Chỉ cho phép upload 1 file
  });

  return (
    <div 
      className={`flex pt-2 w-full ${className}`}
      style={{ height: FRAME_HEIGHT + 20 }}
    >
      <div
        {...getRootProps()}
        className={`relative border-2 border-dashed rounded-md flex flex-col justify-center items-center cursor-pointer
          ${isDragActive ? 'border-blue-500 bg-blue-50' : 'border-gray-300'}
          ${previewImage ? 'bg-gray-100' : 'bg-white hover:bg-gray-50'}`}
        style={{
          width: FRAME_WIDTH,
          height: FRAME_HEIGHT,
          overflow: 'hidden',
        }}
      >
        <input {...getInputProps()} id={id} name={name} />
        {previewImage ? (
          <>
            <img
              src={previewImage}
              alt="Preview"
              className="absolute inset-0 object-cover w-full h-full"
            />
            <div className="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
              <p className="text-white text-sm font-medium">Click or drag to replace</p>
            </div>
          </>
        ) : (
          <div className="text-center p-4">
            <p className="mt-1 text-sm text-gray-500">
              {isDragActive 
                ? "Drop the file here..." 
                : "Click or drag image here"}
            </p>
          </div>
        )}
      </div>
    </div>
  );
};

export default InputImage;