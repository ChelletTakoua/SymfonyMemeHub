import * as htmlToImage from "html-to-image";
import Spinner from "./Spinner";

export default function DownloadBtn({ isLoading, setIsLoading }) {
  async function download() {
    try {
      setIsLoading(true);
      const dataUrl = await htmlToImage.toPng(document.querySelector("#meme"), {
        quality: 1,
      });
      var link = document.createElement("a");
      link.download = "meme.jpeg";
      link.href = dataUrl;
      link.click();
    } catch (error) {
      console.error("Error downloading meme:", error);
    } finally {
      setIsLoading(false);
    }
  }
  return (
    <button
      onClick={download}
      disabled={isLoading}
      className={`w-52 flex justify-center 
      ${!isLoading && "active:scale-95 hover:scale-105 focus:scale-105"}
      transition-transform text-xl py-4 px-6 text-white bg-gradient-to-r from-algae to-grass shadow-2xl rounded-2xl`}
    >
      {!isLoading ? (
        "Download Meme"
      ) : (
        <Spinner width={40} height={28} color="#97da7d" />
      )}
    </button>
  );
}
