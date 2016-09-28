//
//  ViewController.swift
//  测试PHP
//
//  Created by 任前辈 on 16/9/28.
//  Copyright © 2016年 任前辈. All rights reserved.
//

import UIKit

class ViewController: UIViewController {

    override func viewDidLoad() {
        super.viewDidLoad()
         let request = NSMutableURLRequest.init(url: NSURL.init(string: "http://rqb.local/rqbServer.php?name=lhh") as! URL)
//        request.httpBody =  "name=lhh".data(using: .utf8)
//        request.httpMethod  = "POST"
        NSURLConnection.sendAsynchronousRequest(request as URLRequest, queue: OperationQueue.main) { (response, data, error) in
//            print(response,data,error)
            print("==============" + String.init(data: data!, encoding: .utf8)! + "==============")
            let obj = try! JSONSerialization.jsonObject(with: data!, options: .allowFragments)
            
            print(response)
            print(obj)
        }
        // Do any additional setup after loading the view, typically from a nib.
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    /*
     Optional(<NSHTTPURLResponse: 0x608000030560> { URL: http://rqb.local/rqbServer.php?name=lhh } { status code: 200, headers {
     Connection = "Keep-Alive";
     "Content-Length" = 83;
     "Content-Type" = "text/html";
     Date = "Wed, 28 Sep 2016 08:50:15 GMT";
     "Keep-Alive" = "timeout=5, max=100";
     Server = "Apache/2.4.18 (Unix) PHP/5.5.34";
     "X-Powered-By" = "PHP/5.5.34";
     } })
 
     Optional(<NSHTTPURLResponse: 0x600000036720> { URL: http://rqb.local/rqbServer.php?name=lhh } { status code: 200, headers {
     Connection = "Keep-Alive";
     "Content-Length" = 83;
     "Content-Type" = dandanteng;
     Date = "Wed, 28 Sep 2016 08:51:14 GMT";
     "Keep-Alive" = "timeout=5, max=100";
     Server = "Apache/2.4.18 (Unix) PHP/5.5.34";
     "X-Powered-By" = "PHP/5.5.34";
     } })
     
     
     
     
     */


}

