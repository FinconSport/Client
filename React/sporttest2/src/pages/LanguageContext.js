// LanguageContext.js
import twTranslation from '../lang/tw';

const langList = {
    tw: twTranslation,
};

// 預設語言
const langText = langList[window.lang]

export { langText };