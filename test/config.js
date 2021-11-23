const MOODLE_URL = "http://bryan-pc.internal:8081/moodle/official";
const MOODLE_COURSE = "/course/view.php?id=2";
const MOODLE_SECTION = "#section-5";

const MOODLE_PAGE = `${MOODLE_URL}${MOODLE_COURSE}`;


const STORAGE_FILE = 'storageState.json';
const BIG_VIEWPORT = {
    width: 1890,
    height: 930
  };

module.exports = {
    MOODLE_URL,
    MOODLE_COURSE,
    MOODLE_SECTION,
    MOODLE_PAGE,
    STORAGE_FILE,
    BIG_VIEWPORT
}