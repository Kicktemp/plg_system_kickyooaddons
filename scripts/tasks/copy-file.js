import fs from 'fs-extra';
import stripJsonComments from 'strip-json-comments';
import {dirname} from 'path';
import {stringsreplace} from '../../config.js';

export const copyFile = async (src, dest, manipulateData = false) => {
  fs.promises.readFile(src, 'utf8')
    .then((data) => {
    if (
        src == 'src/structure/plugins/system/kickyooaddons/modules/Core/Src/languages/en_GB.json' ||
        src == 'src/structure/plugins/system/kickyooaddons/modules/Core/Src/languages/de_DE.json'
    ){
        console.log('test');
        data = JSON.stringify(JSON.parse(stripJsonComments(data)));
    }

      if (manipulateData) {
        for (let [key, value] of Object.entries(stringsreplace)) {
          key = key.replace('[', '\\[');
          key = key.replace(']', '\\]');
          var re = new RegExp(key, 'g');
          data = data.replace(re, value);
        }
      }

      fs.promises.mkdir(dirname(dest), {recursive: true})
        .then(
          x => fs.promises.writeFile(dest, data, 'utf8')
            .catch((err) => {
              return console.log(err);
            })
        )
    })
}
