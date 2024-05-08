import Spinner from "./Spinner";

export default function SaveBtn({ onClick, isLoading }) {
  return (
    <button
      className={`w-52 flex justify-center content-center
    ${!isLoading && "active:scale-95 hover:scale-105 focus:scale-105"}
    transition-transform text-xl py-4 px-6 text-white bg-gradient-to-r from-algae to-grass shadow-2xl rounded-2xl`}
      disabled={isLoading}
      onClick={onClick}
    >
      {!isLoading ? (
        "Save Meme"
      ) : (
        <Spinner width={40} height={28} color="#97da7d" />
      )}
    </button>
  );
}
