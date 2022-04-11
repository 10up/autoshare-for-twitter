/**
 * Get value for elements specified attribute.
 *
 * @param {Number} length - String length.
 * @return {string} - Returns the random string.
 */
export const getRandomText = ( length ) => {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() *
                    charactersLength));
        }
        return result;
};